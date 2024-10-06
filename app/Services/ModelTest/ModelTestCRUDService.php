<?php

namespace App\Services\ModelTest;


use App\Models\ModelTest;

class ModelTestService
{
    public function getModelTests($perPage = 15)
    {
        return ModelTest::showAllWithCategory()->paginate($perPage);
    }
    public function createModelTestWithCategory($modelTestData, $categoryData)
    {
        $modelTest = new ModelTest();
        return $modelTest->createWithCategory($modelTestData, $categoryData);
    }

    public function updateModelTestWithCategory($modelTestData, $categoryData)
    {
      
    }

    public function deleteModelTestWithCategory($id)
    {
        $modelTest = ModelTest::findOrFail($id);
        return $modelTest->deleteWithCategory();
    }

    public function getModelTestWithCategory($id)
    {
        return ModelTest::showWithCategory($id);
    }

    public function changeStatus($id, $status)
    {
        $modelTest = ModelTest::findOrFail($id);
        return $modelTest->update(['status' => $status]);
    }
}
