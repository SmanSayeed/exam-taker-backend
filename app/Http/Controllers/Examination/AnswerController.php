<?php

namespace App\Http\Controllers\Examination;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use Illuminate\Http\Request;
use App\Models\Examination;
use App\Models\Question;
use Carbon\Carbon;
use App\Http\Requests\StartExamRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class AnswerController extends Controller
{


    public function formatQuestionData($questions, $type = null)
    {
        $query = Question::whereIn('id', $questions);

        // Filter by type, if provided
        if ($type) {
            $query->where('type', $type);
        }

        // Include related data based on the question type
        switch ($type) {
            case 'mcq':
                $query->with('mcqQuestions');
                break;
            case 'creative':
                $query->with('creativeQuestions');
                break;
            case 'normal':
                // No additional relations for normal questions
                break;
            default:
                // Include both mcq and creative options
                $query->with(['creativeQuestions', 'mcqQuestions']);
                break;
        }

        // Always include the attachable relation
        return $query->with('attachable')->get();
    }

 // Function to start an exam
 public function startExam(Request $request)
 {
     // Validate incoming request
     $validator = Validator::make($request->all(), [
         'examination_id' => 'required|exists:examinations,id',
         'student_id' => 'required|exists:students,id',
         'type' => 'required|in:mcq,creative,normal',
     ]);

     if ($validator->fails()) {
         return response()->json(['error' => $validator->errors()], 400);
     }

     // Check if the student has already started this exam
     $existingAnswer = Answer::where('examination_id', $request->examination_id)
         ->where('student_id', $request->student_id)
         ->first();

     if ($existingAnswer) {
         return response()->json(['error' => 'Exam already started for this student.'], 400);
     }

     // Start the exam and insert initial data into the answers table
     $answer = Answer::create([
         'examination_id' => $request->examination_id,
         'student_id' => $request->student_id,
         'type' => $request->type,
         'exam_start_time' => now(),
         'is_second_timer' => $request->is_second_timer ?? false, // Optional field
     ]);

     return response()->json(['message' => 'Exam started successfully', 'answer' => $answer], 201);
 }

 // Function to finish the exam and submit answers
 public function finishExam(Request $request)
 {
     // Validate incoming request
     $validator = Validator::make($request->all(), [
         'examination_id' => 'required|exists:examinations,id',
         'student_id' => 'required|exists:students,id',
         'mcq_answers' => 'nullable|array',
         'mcq_answers.*.question_id' => 'required_with:mcq_answers|integer|exists:questions,id',
         'mcq_answers.*.mcq_question_id' => 'required_with:mcq_answers|integer',
         'mcq_answers.*.submitted_mcq_option' => 'required_with:mcq_answers|string',
         'creative_answers' => 'nullable|array',
         'creative_answers.*.question_id' => 'required_with:creative_answers|integer|exists:questions,id',
         'creative_answers.*.creative_question_id' => 'required_with:creative_answers|integer',
         'creative_answers.*.creative_question_option' => 'required_with:creative_answers|string',
         'creative_answers.*.creative_question_text' => 'required_with:creative_answers|string',
         'normal_answers' => 'nullable|array',
         'normal_answers.*.question_id' => 'required_with:normal_answers|integer|exists:questions,id',
         'normal_answers.*.normal_answer_text' => 'required_with:normal_answers|string',
     ]);

     if ($validator->fails()) {
         return response()->json(['error' => $validator->errors()], 400);
     }

     // Find the student's exam attempt
     $answer = Answer::where('examination_id', $request->examination_id)
         ->where('student_id', $request->student_id)
         ->first();
     if (!$answer) {
         return response()->json(['error' => 'No active exam found for this student.'], 404);
     }

     // Retrieve examination details
     $examination = Examination::find($request->examination_id);
     if (!$examination) {
         return response()->json(['error' => 'Examination not found.'], 404);
     }

     // Initialize response arrays
     $mcqAnswers = [];
     $creativeAnswers = [];
     $normalAnswers = [];
     $totalMarks = 0;
     $correctCount = 0;
     $totalQuestionsCount = 0;

     // Retrieve questions and their details
     $questions = explode(',', $examination->questions);
     $formattedQuestions = $this->formatQuestionData($questions, $examination->type);

     // Process MCQ answers
     if ($examination->type == 'mcq') {
         $totalQuestionsCount = count($formattedQuestions);

         foreach ($formattedQuestions as $question) {
             $mcqAnswer = [
                 'question_id' => $question->id,
                 'mcq_question_id' => $question->id, // Ensure it's not null
                 'submitted_mcq_option' => null,
                 'is_correct' => false,
                 'description' => $question->description,
                 'mcq_question_text' => $question->title, // Assuming title is the question text
                 'mcq_images' => $question->images,
             ];

             foreach ($request->mcq_answers ?? [] as $submittedAnswer) {
                 if ($submittedAnswer['mcq_question_id'] == $question->id) {
                     $mcqAnswer['submitted_mcq_option'] = $submittedAnswer['submitted_mcq_option'];
                     $mcqAnswer['is_correct'] = $submittedAnswer['submitted_mcq_option'] == $question->correct_option; // Assuming correct_option holds the correct answer
                     if ($mcqAnswer['is_correct']) {
                         $correctCount++;
                         $totalMarks += $question->mark;
                     }
                     break;
                 }
             }

             $mcqAnswers[] = $mcqAnswer;
         }

         // Update the answer record with MCQ data
         $answer->mcq_answers = $mcqAnswers;
     }

     // Process Creative answers
     if ($examination->type == 'creative') {
         $totalQuestionsCount = count($formattedQuestions);

         foreach ($formattedQuestions as $question) {
             $creativeAnswer = [
                 'question_id' => $question->id,
                 'creative_question_id' => $request->creative_answers->firstWhere('question_id', $question->id)['creative_question_id'] ?? null,
                 'creative_question_option' => $request->creative_answers->firstWhere('question_id', $question->id)['creative_question_option'] ?? null,
                 'creative_question_text' => $request->creative_answers->firstWhere('question_id', $question->id)['creative_question_text'] ?? null,
             ];

             $creativeAnswers[] = $creativeAnswer;
         }

         // Update the answer record with Creative data
         $answer->creative_answers = $creativeAnswers;
     }

     // Process Normal answers
     if ($examination->type == 'normal') {
         $totalQuestionsCount = count($formattedQuestions);

         foreach ($formattedQuestions as $question) {
             $normalAnswer = [
                 'question_id' => $question->id,
                 'normal_answer_text' => $request->normal_answers->firstWhere('question_id', $question->id)['normal_answer_text'] ?? null,
             ];

             $normalAnswers[] = $normalAnswer;
         }

         // Update the answer record with Normal data
         $answer->normal_answers = $normalAnswers;
     }

     // Finalize and save the answer record
     $answer->submission_time = now();
     $answer->is_answer_submitted = true;
     $answer->total_marks = $totalMarks;
     $answer->correct_count = $correctCount;
     $answer->total_questions_count = $totalQuestionsCount;

     $answer->save();

     // Prepare response data
     $response = [
         'examination' => $examination,
         'student' => $examination->student,
         'mcq_answers' => $mcqAnswers,
         'creative_answers' => $creativeAnswers,
         'normal_answers' => $normalAnswers,
     ];

     return response()->json($response);
 }




}
