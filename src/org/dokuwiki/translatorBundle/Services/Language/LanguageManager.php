<?php
namespace org\dokuwiki\translatorBundle\Services\Language;

use Symfony\Component\HttpFoundation\Request;

class LanguageManager {

    /**
     * read languages from lang folder.
     *
     * @param string $langFolder Lang folder
     * @param string $prefix Prefix string for item keys in language array.
     * @throws NoLanguageFolderException
     * @throws NoDefaultLanguageException
     * @return array()
     */
    public static function readLanguages($langFolder, $prefix = '') {
        if (!is_dir($langFolder)) {
            throw new NoLanguageFolderException();
        }

        if (!is_dir("$langFolder/en/")) {
            throw new NoDefaultLanguageException();
        }

        $folders = scandir($langFolder);
        $languages = array();
        foreach ($folders as $language) {
            if ($language === '.' || $language === '..') {
                continue;
            }
            if (!is_dir("$langFolder/$language")) {
                continue;
            }
            $languages[$language] = LanguageManager::readLanguage("$langFolder/$language", "$prefix");
        }
        return $languages;
    }

    private static function readLanguage($languageFolder, $prefix) {
        $language = array();

        $folders = scandir($languageFolder);
        foreach ($folders as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            if (!is_file("$languageFolder/$file")) {
                continue;
            }

            $extension = substr($file, -4);

            if ($extension === '.php') {
                $translation = LanguageFileParser::parseLangPHP("$languageFolder/$file");
                $language[$prefix . $file] =
                        new LocalText($translation->getLang(), LocalText::$TYPE_ARRAY, $translation->getAuthor());
                continue;
            }

            if ($extension === '.txt') {
                $language[$prefix . $file] = new LocalText(file_get_contents("$languageFolder/$file"), LocalText::$TYPE_MARKUP);
            }

        }
        return $language;
    }

    public function getLanguage(Request $request) {
        $language = $request->query->get('lang', null);
        if ($language !== null) {
            $request->getSession()->set('language', $language);
            return $language;
        }
        $sessionLanguage = $request->getSession()->get('language');
        if ($sessionLanguage !== null) {
            return $sessionLanguage;
        }
        $languages = $request->getLanguages();
        if (empty($languages)) {
            $request->getSession()->set('language', 'en');
            return 'en';
        }
        $pos = strpos($languages[0], '_');
        if ($pos !== false) {
            $languages[0] = substr($languages[0], 0, $pos);
        }
        $language = strtolower($languages[0]);
        $request->getSession()->set('language', $language);
        return $language;
    }
}
