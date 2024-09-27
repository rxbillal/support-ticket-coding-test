<?php

namespace App\Repositories;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class TranslationManagerRepository
 */
class TranslationManagerRepository
{
    public function store($input)
    {
        $allLanguagesArr = [];
        $languages = File::directories(App::langPath());
        foreach ($languages as $language) {
            $allLanguagesArr[] = substr($language, -2);
        }

        if (in_array(strtolower($input['code']), $allLanguagesArr)) {
            throw new UnprocessableEntityHttpException($input['code'].' language already exists.');
        }

        try {
            if (! empty($input['code'])) {
                //Make directory in lang folder
                File::makeDirectory(resource_path('/lang/').strtolower($input['code']));

                //Copy all en folder files to new folder.
                $filesInFolder = File::files(App::langPath().'/en');
                foreach ($filesInFolder as $path) {
                    $file = basename($path);
                    File::copy(App::langPath().'/en/'.$file, App::langPath().'/'.strtolower($input['code']).'/'.$file);
                }

                //append new language and code in helpers file
                $fileName = app_path('helpers.php');
                $search = "";
                foreach (LANGUAGES as $code => $langName) {
                    $search .= "    '$code' => '$langName',\n";
                }
                $value = $search ."    '".strtolower($input['code'])."' => '".ucfirst($input['name'])."',\n";
                file_put_contents(
                    $fileName,
                    str_replace($search, $value, file_get_contents($fileName))
                );
            }

            return true;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }
    
    /**
     * @param $lang
     *
     * @return array
     */
    public function getAllFiles($lang)
    {
        $files = File::allFiles(App::langPath().'/'.$lang.'/');
        $allFilesArr = [];
        foreach ($files as $file) {
            $allFilesArr[] = basename($file, '.php');
        }

        return $allFilesArr;
    }

    /**
     * @param $selectedLang
     * @param $selectedFile
     *
     * @return array
     */
    public function getAllLanguage($selectedLang, $selectedFile)
    {
        $allLanguagesArr = $selectedLangMessages = [];
        $allLanguagesFiles = File::directories(App::langPath());
        $oldLang = app()->getLocale();
        foreach ($allLanguagesFiles as $language) {
            $allLanguagesArr[] = substr($language, -2);
            app()->setLocale(substr($selectedLang, -2));
            $selectedLangMessages = trans($selectedFile);
        }
        app()->setLocale($oldLang);

        return ['allLanguagesArr' => $allLanguagesArr, 'selectedLangMessages' => $selectedLangMessages];
    }

    /**
     * @param $lang
     * @param $file
     * @param $result
     */
    public function update($lang, $file, $result)
    {
        $oldLang = app()->getLocale();
        app()->setLocale($lang);
        $selectedLangMessages = trans($file);
        app()->setLocale($oldLang);

        $diff = array_diff(array_map('serialize', $result), array_map('serialize', $selectedLangMessages));
        $multidimensional_diff = array_map('unserialize', $diff);
        if (count($multidimensional_diff)) {
            $fileName = resource_path("lang/$lang/$file.php");
            foreach ($multidimensional_diff as $key => $value) {
                $value = str_replace("'", "\'", $value);
                if (is_array($selectedLangMessages[$key])) {
                    continue;
                }
                $searchValue = str_replace("'", "\'", $selectedLangMessages[$key]);
                try {
                    file_put_contents($fileName, str_replace($searchValue, $value, file_get_contents($fileName)));
                } catch (\Exception $e) {

                }
            }
            /*            File::put(resource_path("lang/$lang/$file.php"), '<?php return ' . var_export($result, true) . '?>');*/
        }
    }
}
