<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

final class AdminController extends Controller
{
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // тільки авторизовані
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }
}
