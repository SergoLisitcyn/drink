<?php

namespace common\models;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string|null $category
 * @property string|null $name
 * @property string|null $image
 * @property int $price
 * @property string $sort
 */
class Category extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price'], 'integer'],
            [['sort'], 'string'],
            [['category', 'name', 'image'], 'string', 'max' => 255],
            [['file'], 'file'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category' => 'Категория',
            'name' => 'Название',
            'image' => 'Картинка',
            'price' => 'Цена',
            'sort' => 'Статус',
        ];
    }



    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $imageSquareFile = UploadedFile::getInstance($this, 'file');
        if ($imageSquareFile) {
            $directory = Yii::getAlias('@frontend/web/uploads/images/category') . DIRECTORY_SEPARATOR;
            if (!is_dir($directory)) {
                FileHelper::createDirectory($directory);
            }

            $uid = date('YmdHs').Yii::$app->security->generateRandomString(6);
            $fileName = $uid . '-image.' . $imageSquareFile->extension;
            $filePath = $directory . $fileName;
            if ($imageSquareFile->saveAs($filePath)) {
                $path = '/uploads/images/category/' . $fileName;

                @unlink(Yii::getAlias('@frontend/web') . $this->file);
                $this->setAttribute('image', $path);
                $this->save();
            }
        }
    }
}
