<?php

namespace App\Http\Controllers\Api\V1\Questions;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\McqQuestion;
use App\Models\CreativeQuestion;
use App\Models\Questionable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManageQuestionController extends Controller
{
    /**
     * Create a new question with its associated data.
     */
    public function create(Request $request)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'string|url',
                'is_paid' => 'required|boolean',
                'is_featured' => 'required|boolean',
                'type' => 'required|in:mcq,creative,normal',
                'mark' => 'required|integer',
                'status' => 'boolean',
                'mcq_options' => 'required_if:type,mcq|array',
                'mcq_options.*.mcq_question_text' => 'required_if:type,mcq|string',
                'mcq_options.*.mcq_images' => 'nullable|array',
                'mcq_options.*.mcq_images.*' => 'string|url',
                'mcq_options.*.is_correct' => 'required_if:type,mcq|boolean',
                'creative_options' => 'required_if:type,creative|array',
                'creative_options.*.creative_question_text' => 'required_if:type,creative|string',
                'creative_options.*.creative_question_type' => 'required_if:type,creative|in:a,b,c,d',
                'categories' => 'nullable|array',
                'categories.section_id' => 'nullable|integer|exists:sections,id',
                'categories.exam_type_id' => 'nullable|integer|exists:exam_types,id',
                'categories.exam_sub_type_id' => 'nullable|integer|exists:exam_sub_types,id',
                'categories.group_id' => 'nullable|integer|exists:groups,id',
                'categories.level_id' => 'nullable|integer|exists:levels,id',
                'categories.subject_id' => 'nullable|integer|exists:subjects,id',
                'categories.lesson_id' => 'nullable|integer|exists:lessons,id',
                'categories.topic_id' => 'nullable|integer|exists:topics,id',
                'categories.sub_topic_id' => 'nullable|integer|exists:sub_topics,id',
            ]);

            // Enforce hierarchical rules on categories
            $this->validateCategoryHierarchy($validated['categories']);

            // Create the question inside a transaction
            DB::beginTransaction();

            $question = Question::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'images' => isset($validated['images']) ? json_encode($validated['images']) : null,
                'is_paid' => $validated['is_paid'],
                'is_featured' => $validated['is_featured'],
                'type' => $validated['type'],
                'mark' => $validated['mark'],
                'status' => $validated['status'] ?? true,
            ]);

            // Handle MCQ options
            if ($validated['type'] === 'mcq' && isset($validated['mcq_options'])) {
                foreach ($validated['mcq_options'] as $option) {
                    McqQuestion::create([
                        'question_id' => $question->id,
                        'mcq_question_text' => $option['mcq_question_text'],
                        'mcq_images' => isset($option['mcq_images']) ? json_encode($option['mcq_images']) : null,
                        'is_correct' => $option['is_correct'],
                        'description' => $option['description'] ?? null,
                    ]);
                }
            }

            // Handle Creative options
            if ($validated['type'] === 'creative' && isset($validated['creative_options'])) {
                foreach ($validated['creative_options'] as $option) {
                    CreativeQuestion::create([
                        'question_id' => $question->id,
                        'creative_question_text' => $option['creative_question_text'],
                        'creative_question_type' => $option['creative_question_type'],
                        'description' => $option['description'] ?? null,
                    ]);
                }
            }

            // Handle category data
            if (isset($validated['categories'])) {
                $this->storeCategories($question->id, $validated['categories']);
            }

            DB::commit();

            // Load related data for the response
            $question->load([
                'mcqQuestions',
                'creativeQuestions',
                'sections',
                'examTypes',
                'examSubTypes',
                'groups',
                'levels',
                'subjects',
                'lessons',
                'topics',
                'subTopics'
            ]);

            return response()->json(['status' => 'success', 'message' => 'Question created successfully', 'data' => $question], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. ' . $e->getMessage(),
                'errors' => [],
            ], 500);
        }
    }

    /**
     * Update an existing question.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the request data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'images' => 'nullable|array',
                'images.*' => 'string|url',
                'is_paid' => 'required|boolean',
                'is_featured' => 'required|boolean',
                'type' => 'required|in:mcq,creative,normal',
                'mark' => 'required|integer',
                'status' => 'boolean',
                'mcq_options' => 'nullable|array',
                'mcq_options.*.mcq_question_text' => 'required_if:type,mcq|string',
                'mcq_options.*.mcq_images' => 'nullable|array',
                'mcq_options.*.mcq_images.*' => 'string|url',
                'mcq_options.*.is_correct' => 'required_if:type,mcq|boolean',
                'creative_options' => 'nullable|array',
                'creative_options.*.creative_question_text' => 'required_if:type,creative|string',
                'creative_options.*.creative_question_type' => 'required_if:type,creative|in:a,b,c,d',
                'categories' => 'nullable|array',
                'categories.section_id' => 'nullable|integer|exists:sections,id',
                'categories.exam_type_id' => 'nullable|integer|exists:exam_types,id',
                'categories.exam_sub_type_id' => 'nullable|integer|exists:exam_sub_types,id',
                'categories.group_id' => 'nullable|integer|exists:groups,id',
                'categories.level_id' => 'nullable|integer|exists:levels,id',
                'categories.subject_id' => 'nullable|integer|exists:subjects,id',
                'categories.lesson_id' => 'nullable|integer|exists:lessons,id',
                'categories.topic_id' => 'nullable|integer|exists:topics,id',
                'categories.sub_topic_id' => 'nullable|integer|exists:sub_topics,id',
            ]);

            // Enforce hierarchical rules on categories for updating
            $this->validateCategoryHierarchy($validated['categories'], $id);

            // Find the question by ID
            $question = Question::findOrFail($id);

            // Start the transaction
            DB::beginTransaction();

            // Update the question
            $question->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'images' => isset($validated['images']) ? json_encode($validated['images']) : $question->images,
                'is_paid' => $validated['is_paid'],
                'is_featured' => $validated['is_featured'],
                'type' => $validated['type'],
                'mark' => $validated['mark'],
                'status' => $validated['status'] ?? true,
            ]);

            // Update MCQ options if type is mcq
            if ($validated['type'] === 'mcq' && isset($validated['mcq_options'])) {
                // Delete existing MCQ options
                McqQuestion::where('question_id', $question->id)->delete();

                // Create new MCQ options
                foreach ($validated['mcq_options'] as $option) {
                    McqQuestion::create([
                        'question_id' => $question->id,
                        'mcq_question_text' => $option['mcq_question_text'],
                        'mcq_images' => isset($option['mcq_images']) ? json_encode($option['mcq_images']) : null,
                        'is_correct' => $option['is_correct'],
                        'description' => $option['description'] ?? null,
                    ]);
                }
            }

            // Update Creative options if type is creative
            if ($validated['type'] === 'creative' && isset($validated['creative_options'])) {
                // Delete existing Creative options
                CreativeQuestion::where('question_id', $question->id)->delete();

                // Create new Creative options
                foreach ($validated['creative_options'] as $option) {
                    CreativeQuestion::create([
                        'question_id' => $question->id,
                        'creative_question_text' => $option['creative_question_text'],
                        'creative_question_type' => $option['creative_question_type'],
                        'description' => $option['description'] ?? null,
                    ]);
                }
            }

            // Update categories if provided
            if (isset($validated['categories'])) {
                $this->storeCategories($question->id, $validated['categories']);
            }

            DB::commit();

            // Load related data for the response
            $question->load([
                'mcqQuestions',
                'creativeQuestions',
                'sections',
                'examTypes',
                'examSubTypes',
                'groups',
                'levels',
                'subjects',
                'lessons',
                'topics',
                'subTopics'
            ]);

            return response()->json(['status' => 'success', 'message' => 'Question updated successfully', 'data' => $question], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. ' . $e->getMessage(),
                'errors' => [],
            ], 500);
        }
    }

    /**
     * Validate the hierarchy of categories.
     */
    protected function validateCategoryHierarchy($categories, $questionId = null)
    {
        if (isset($categories['exam_sub_type_id']) && !isset($categories['exam_type_id'])) {
            throw ValidationException::withMessages(['exam_type_id' => 'Exam type is required for an exam sub type.']);
        }

        if (isset($categories['exam_type_id']) && !isset($categories['section_id'])) {
            throw ValidationException::withMessages(['section_id' => 'Section is required for an exam type.']);
        }

        if (isset($categories['sub_topic_id']) && !isset($categories['topic_id'])) {
            throw ValidationException::withMessages(['topic_id' => 'Topic is required for a sub-topic.']);
        }

        if (isset($categories['topic_id']) && !isset($categories['lesson_id'])) {
            throw ValidationException::withMessages(['lesson_id' => 'Lesson is required for a topic.']);
        }

        if (isset($categories['lesson_id']) && !isset($categories['subject_id'])) {
            throw ValidationException::withMessages(['subject_id' => 'Subject is required for a lesson.']);
        }

        if (isset($categories['subject_id']) && !isset($categories['level_id'])) {
            throw ValidationException::withMessages(['level_id' => 'Level is required for a subject.']);
        }

        if (isset($categories['level_id']) && !isset($categories['group_id'])) {
            throw ValidationException::withMessages(['group_id' => 'Group is required for a level.']);
        }

        // For editing, ensure child categories are removed before removing parent categories
        if ($questionId) {
            $question = Question::with('sections', 'examTypes', 'examSubTypes', 'groups', 'levels', 'subjects', 'lessons', 'topics', 'subTopics')->findOrFail($questionId);

            if (!$categories['exam_sub_type_id'] && $question->examSubTypes()->exists()) {
                throw ValidationException::withMessages(['exam_sub_type_id' => 'Exam sub type must be removed before removing exam type.']);
            }

            if (!$categories['exam_type_id'] && $question->examTypes()->exists()) {
                throw ValidationException::withMessages(['exam_type_id' => 'Exam type must be removed before removing section.']);
            }

            if (!$categories['sub_topic_id'] && $question->subTopics()->exists()) {
                throw ValidationException::withMessages(['sub_topic_id' => 'Sub-topic must be removed before removing topic.']);
            }

            if (!$categories['topic_id'] && $question->topics()->exists()) {
                throw ValidationException::withMessages(['topic_id' => 'Topic must be removed before removing lesson.']);
            }

            if (!$categories['lesson_id'] && $question->lessons()->exists()) {
                throw ValidationException::withMessages(['lesson_id' => 'Lesson must be removed before removing subject.']);
            }

            if (!$categories['subject_id'] && $question->subjects()->exists()) {
                throw ValidationException::withMessages(['subject_id' => 'Subject must be removed before removing level.']);
            }

            if (!$categories['level_id'] && $question->levels()->exists()) {
                throw ValidationException::withMessages(['level_id' => 'Level must be removed before removing group.']);
            }
        }
    }

    /**
     * Store category data in the questionables table.
     */
    protected function storeCategories($questionId, $categories)
    {
        $categoriesData = [
            'section_id' => $categories['section_id'] ?? null,
            'exam_type_id' => $categories['exam_type_id'] ?? null,
            'exam_sub_type_id' => $categories['exam_sub_type_id'] ?? null,
            'group_id' => $categories['group_id'] ?? null,
            'level_id' => $categories['level_id'] ?? null,
            'subject_id' => $categories['subject_id'] ?? null,
            'lesson_id' => $categories['lesson_id'] ?? null,
            'topic_id' => $categories['topic_id'] ?? null,
            'sub_topic_id' => $categories['sub_topic_id'] ?? null,
        ];

        // Remove old entries
        Questionable::where('question_id', $questionId)->delete();

        // Insert new entries
        foreach (array_filter($categoriesData) as $key => $value) {
            Questionable::updateOrCreate(
                ['question_id' => $questionId],
                [$key => $value]
            );
        }
    }
}
