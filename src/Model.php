<?php


class Model
{
    private $verts = [];
    private $faces = [];

    public function __construct($path)
    {
        if (!file_exists($path)) {
            echo "File not found";
        }

        $file = fopen($path, 'r');
        if ($file) {
            while (($buffer = fgets($file)) !== false) {

                $this->parseLine(rtrim($buffer));
            }

            if (!feof($file)) {
                echo "Ошибка: fgets() неожиданно потерпел неудачу\n";
            }
            fclose($file);
        }
    }

    protected function parseLine(string $line)
    {
        $res = explode(' ', $line);
        if (!$res) {
            echo 'parse error';
        }
        switch ($res[0]) {
            case 'v':
                $this->verts[] = new Vec3($res[1], $res[2], $res[3]);
                break;
            case 'f':
                $size = count($res) - 1;
                $face = [];
                for ($i = 1; $i <= $size; $i++) {
                    $point = explode('/', $res[$i]);
                    $face[] = $point[0];
                }
                $this->faces[] = $face;
                break;
        }
    }

    /**
     * @param int $i
     * @return array|null
     */
    public function face(int $i)
    {
        return $this->faces[$i] ?? null;
    }

    /**
     * @param int $i
     * @return Vec3|null
     */
    public function vert(int $i) {
        return $this->verts[$i - 1] ?? null;
    }

    public function nfaces()
    {
        return count($this->faces);
    }

    public function nverts()
    {
        return count($this->verts);
    }
}