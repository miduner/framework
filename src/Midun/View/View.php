<?php

namespace Midun\View;

class View
{
    /**
     * Constant synchronous view mode
     * 
     * @var string
     */
    const SYNC = 'sync';

    /**
     * Constant caching view mode
     * 
     * @var string
     */
    const CACHE = 'cache';

    /**
     * Master layout
     * 
     * @var string
     */
    protected $masterLayout;

    /**
     * Directory of views
     * 
     * @var string
     */
    protected $directory;

    /**
     * Directory of cache views
     * 
     * @var string
     */
    protected $cacheDirectory;

    /**
     * Current working section
     * 
     * @var string
     */
    protected $section;

    /**
     * List of sections
     * 
     * @var array
     */
    protected $sections = [];

    /**
     * List of php start tags
     * 
     * @var array
     */
    const START_TAGS = [
        "{{",
        "@php"
    ];

    /**
     * List of php end tags
     * 
     * @var array
     */
    const END_TAGS = [
        "}}",
        "@endphp"
    ];

    /**
     * Initial constructor of views
     * 
     * @param string $directory
     * @param string $cacheDirectory
     */
    public function __construct(string $directory, string $cacheDirectory)
    {
        $this->directory = $directory;
        $this->cacheDirectory = $cacheDirectory;
        ob_get_clean();
    }

    /**
     * Get directory
     * 
     * @return string
     */
    public function getDirectory()
    {
        return str_replace("/", DIRECTORY_SEPARATOR, $this->directory);
    }

    /**
     * Get caching directory
     * 
     * @return string
     */
    public function getCachingDirectory()
    {
        return str_replace("/", DIRECTORY_SEPARATOR, $this->cacheDirectory);
    }

    /**
     * Set master layout
     * 
     * @param string $masterCLayout
     * 
     * @return void
     */
    public function setMaster(string $masterLayout)
    {
        $this->masterLayout = $masterLayout;
    }

    /**
     * Make view caching
     * 
     * @param string $file
     * 
     * @return void
     */
    public function makeCache(string $file)
    {
        $file = strpos($file, '.php') !== false
            ? str_replace('.php', '', $file)
            : $file;

        $file = str_replace('.', DIRECTORY_SEPARATOR, $file) . '.php';

        $viewPath = $this->getDirectory() . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($viewPath)) {
            throw new ViewException("View {$file} not found.");
        }

        $cacheDirectory = $this->getCachingDirectory();

        if (false == is_dir($cacheDirectory)) {
            $dir = '';
            foreach (explode(DIRECTORY_SEPARATOR, $cacheDirectory) as $k => $f) {
                $dir .= $f . DIRECTORY_SEPARATOR;
                if (false === is_dir($dir)) {
                    mkdir($dir);
                }
            }
        }

        $viewData = file_get_contents($viewPath);

        foreach (self::START_TAGS as $tag) {
            $viewData = str_replace($tag, '<?php', $viewData);
        }
        foreach (self::END_TAGS as $tag) {
            $viewData = str_replace($tag, '?>', $viewData);
        }

        $newViewData = [];

        foreach (explode(PHP_EOL, $viewData) as $line) {
            switch (true) {
                case strpos($line, '@if(') !== false:
                case strpos($line, '@foreach(') !== false:
                    $line = str_replace('@', '', $line);
                    $newViewData[] = "<?php {$line}: ?>";
                    break;
                case strpos($line, '@endif') !== false:
                case strpos($line, '@endforeach') !== false:
                    $line = str_replace('@', '', $line);
                    $newViewData[] = "<?php {$line}; ?>";
                    break;
                default:
                    $newViewData[] = $line;
                    break;
            }
        }

        $viewData = implode(PHP_EOL, $newViewData);

        $tickets = explode(DIRECTORY_SEPARATOR, $file);

        $file = array_pop($tickets);

        foreach ($tickets as $f) {
            if ($f != '') {
                $cacheDirectory .= DIRECTORY_SEPARATOR . $f;
            }
            if (!is_dir($cacheDirectory)) {
                mkdir($cacheDirectory);
            }
        }
        if (false == is_dir($cacheDirectory)) {
            mkdir($cacheDirectory);
        }
        $cacheFilePath = $cacheDirectory . DIRECTORY_SEPARATOR . $file;

        $cacheFile = fopen($cacheFilePath, "w") or die("Unable to open file!");
        fwrite($cacheFile, $viewData);
        fclose($cacheFile);
    }

    /**
     * Get content of view from cache file with implement arguments
     * 
     * @param string $file
     * @param array $arguments
     * 
     * @return string
     */
    public function getContentFromCacheWithArguments(string $file, array $arguments)
    {
        $file = str_replace('.', '/', $file) . '.php';

        $fileCachePath = $this->getCachingDirectory() . DIRECTORY_SEPARATOR . $file;

        if (!file_exists($fileCachePath)) {
            throw new ViewException("File $fileCachePath not found");
        }

        ob_start();

        extract($arguments, EXTR_PREFIX_SAME, "data");

        require $fileCachePath;

        return ob_get_clean();
    }

    /**
     * Set current section
     * 
     * @param string $section
     * 
     * @return void
     * 
     * @throws ViewException
     */
    public function setCurrentSection($section)
    {
        if ($this->existsSection()) {
            throw new ViewException("Missing tag `endsection` before start new section.<br>Current section `{$this->section}`");
        }
        $this->section = $section;
    }

    /**
     * Check exists current section
     * 
     * @return bool
     */
    private function existsSection()
    {
        return !is_null(
            $this->getCurrentSection()
        );
    }

    /**
     * Set section with data
     * 
     * @param string $section
     * @param mixed $section
     * 
     * @return void
     */
    public function setSectionWithData(string $section, $data)
    {
        $this->sections[$section] = $data;
    }

    /**
     * Get current section
     * 
     * @return string|null
     */
    private function getCurrentSection()
    {
        return $this->section;
    }

    /**
     * Set data for current section
     * 
     * @param mixed $data
     * 
     * @return void
     */
    public function setDataForSection($data)
    {
        $this->sections[$this->section] = htmlentities($data);
        $this->section = null;
    }

    /**
     * Get list of sections
     * 
     * @return array
     */
    protected function getSections()
    {
        return $this->sections;
    }

    /**
     * Get master layouts
     * 
     * @return string|null
     */
    protected function getMasterLayout()
    {
        return $this->masterLayout;
    }

    /**
     * Get needed section
     * 
     * @param string $section
     * @param string $instead
     * 
     * @return mixed|null
     */
    public function getNeedSection(string $section, string $instead)
    {
        return isset($this->sections[$section])
            ? \html_entity_decode($this->sections[$section])
            : $instead;
    }

    /**
     * Render view
     * 
     * @param string $file
     * @param array $arguments
     * 
     * @return void
     */
    public function render(string $file, array $arguments = [], string $mode = '')
    {
        $mode = $mode ?: env('VIEW_MODE', View::SYNC);

        switch ($mode) {
            case View::CACHE:
                return $this->cachingRendering($file, $arguments);
            case View::SYNC:
                return $this->syncRendering($file, $arguments);
            default:
                $exception = new ViewException("Unknown view rendering mode `{$mode}`");
                return $this->render('exception', compact('exception'), View::SYNC);
        }
    }

    /**
     * Synchronous rendering view
     * 
     * @param string $file
     * @param array $arguments
     * 
     * @return void
     */
    private function syncRendering(string $file, array $arguments)
    {
        $this->makeCache($file);

        ob_start();

        $content = $this->getContentFromCacheWithArguments(
            $file,
            $arguments
        );

        ob_get_clean();

        if ($this->getMasterLayout()) {

            $this->makeCache(
                $this->getMasterLayout()
            );

            $content = $this->getContentFromCacheWithArguments(
                $this->getMasterLayout(),
                $arguments
            );
        }

        eval(' ?>' . $content);
    }

    /**
     * Caching rendering view
     * 
     * @param string $file
     * @param array $arguments
     * 
     * @return void
     */
    private function cachingRendering(string $file, array $arguments)
    {
        ob_start();

        $content = $this->getContentFromCacheWithArguments(
            $file,
            $arguments
        );

        ob_get_clean();

        if ($this->getMasterLayout()) {

            $content = $this->getContentFromCacheWithArguments(
                $this->getMasterLayout(),
                $arguments
            );
        }

        eval(' ?>' . $content);
    }
}
