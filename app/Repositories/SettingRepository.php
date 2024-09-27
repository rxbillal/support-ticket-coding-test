<?php

namespace App\Repositories;

use App\Models\Setting;
use Illuminate\Support\Arr;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\DiskDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileDoesNotExist;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\FileIsTooBig;

/**
 * Class SettingRepository
 */
class SettingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'key',
        'value',
    ];

    /**
     * {@inheritdoc}
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * {@inheritdoc}
     */
    public function model()
    {
        return Setting::class;
    }

    /**
     * @param  array  $input
     *
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     *
     * @return bool
     */
    public function updateSetting($input)
    {
        $inputArr = Arr::except($input, ['_token']);
        foreach ($inputArr as $key => $value) {
            /** @var Setting $setting */
            $setting = Setting::where('key', $key)->first();
            if (! $setting) {
                continue;
            }

            if (in_array($key, ['logo', 'favicon']) && ! empty($value)) {
                $this->fileUpload($setting, $value);
                continue;
            }

            $setting->update(['value' => $value]);
        }

        return true;
    }

    /**
     * @param  Setting  $setting
     * @param $file
     *
     * @throws DiskDoesNotExist
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     *
     * @return mixed
     */
    public function fileUpload($setting, $file)
    {
        $setting->clearMediaCollection(Setting::PATH);
        $media = $setting->addMedia($file)->toMediaCollection(Setting::PATH, config('app.media_disc'));

        $setting->update(['value' => $media->getFullUrl()]);

        return $setting;
    }
}
