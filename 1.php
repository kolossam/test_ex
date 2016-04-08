<?php
/**
 * Created by PhpStorm.
 * User: eavl
 * Date: 08.04.16
 * Time: 10:18
 */


// должна стоять версия PHP не ниже 5.6

class DrawManager
{
    private static $_instance = null;
    private $_shapes = [];

    private function __construct() {
        self::_clearImgPath();
    }
    private function __clone() {}
    private function __wakeup() {}

    public static function getInstance()
    {
        return is_null(self::$_instance) ? new self : self::$_instance;
    }

    public function register(array $shapeConf)
    {
        foreach ($shapeConf as $conf) {
            if (isset($conf['type']) && isset($conf['params']) && class_exists($className = ucfirst($conf['type'])) ) {
                $this->_shapes[] = new $className($conf['params']);
            }
        }
        return $this;
    }

    private static function _clearImgPath()
    {
        $files = glob(Shape::$_imgPath.'*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file))
                unlink($file); // delete file
        }
    }

    public function render()
    {
        foreach ($this->_shapes as $key => $shape) {
            echo ($key+1).'. '.$shape->getName().' <img src="'.$shape->getPath().'" /><br />';
        }
    }
}


abstract class Shape
{
    public static $_imgPath = 'test/';

    protected $_name = 'Фигура';

    protected $_path = null;

    protected $_resource = null;

    public function getName()
    {
        return $this->_name;
    }

    protected static $_defaultParams = [
        'size' => [
            'width' => 300,
            'height' => 300
        ],
        'line_color' => [0, 255, 0],
        'fill_color' => [255, 0, 0],
        'padding' => [
            'width' => 50,
            'height' => 50
        ]
    ];

    protected $_params = [];

    public function __construct($params)
    {
        $this->_setParams($params);
    }

    private function _setParams(array $params)
    {
        $this->_params = array_merge(self::$_defaultParams, $params);
        $this->_setSize();
        $this->_setLineColor();
        $this->_setFillColor();
    }

    protected function _setSize()
    {
        isset($this->_params['size']) && (!is_array($this->_params['size']) || sizeof($this->_params['size']) > 2) && $this->_params['size'] = self::$_defaultParams['size'];
    }

    protected function _setLineColor()
    {
        isset($this->_params['line_color']) && (!is_array($this->_params['line_color']) || sizeof($this->_params['line_color']) != 3) && $this->_params['line_color'] = self::$_defaultParams['line_color'];
    }

    protected function _setFillColor()
    {
        isset($this->_params['fill_color']) && !is_array($this->_params['fill_color']) && $this->_params['fill_color'] = self::$_defaultParams['fill_color'];
    }

    protected function _setpadding()
    {
        isset($this->_params['padding']) && (!is_array($this->_params['padding']) || sizeof($this->_params['padding']) > 2) && $this->_params['padding'] = self::$_defaultParams['padding'];
    }

    public function getPath()
    {
        return $this->_path;
    }

    protected function _save($im)
    {
        $this->_path = self::$_imgPath.time().$this->getName().'.png';

        imagepng($im, $this->_path);
        imagedestroy($im);
    }
}


class Circle extends Shape
{
    protected $_name = 'Круг';

    public function __construct(array $params)
    {
        parent::__construct($params);

        $im = imagecreatetruecolor(
            $this->_params['size']['width'] + 2*$this->_params['padding']['width'],
            $this->_params['size']['width'] + 2*$this->_params['padding']['width']
        );

        $lineColor = imagecolorallocate($im, ...$this->_params['line_color']);
        $fillColor = imagecolorallocate($im, ...$this->_params['fill_color']);

        $args = [
            $im,
            $this->_params['padding']['width']+$this->_params['size']['width']/2,
            $this->_params['padding']['width']+$this->_params['size']['width']/2,
            $this->_params['size']['width'],
            $this->_params['size']['width'],
            0,
            360,
            $fillColor,
            IMG_ARC_PIE
        ];

        imagefilledarc(...$args);
        $args[7] = $lineColor;
        unset($args[8]);
        imagearc(...$args);

        $this->_save($im);
    }
}


class Square extends Shape
{
    protected $_name = 'Прямоуголник';

    public function __construct(array $params)
    {
        parent::__construct($params);

        //$im = imagecreatetruecolor(...array_values($this->_params['size']));
        $im = imagecreatetruecolor(
            $this->_params['size']['width'] + 2*$this->_params['padding']['width'],
            $this->_params['size']['height']+ 2*$this->_params['padding']['height']
        );

        $lineColor = imagecolorallocate($im, ...$this->_params['line_color']);
        $fillColor = imagecolorallocate($im, ...$this->_params['fill_color']);

        $args = [
            $im,
            $this->_params['padding']['width'],
            $this->_params['padding']['height'],
            $this->_params['padding']['width'] + $this->_params['size']['width'],
            $this->_params['padding']['height'] + $this->_params['size']['height'],
            $fillColor
        ];

        // Закрашенный прямоугольник
        imagefilledrectangle(...$args);
        $args[5] = $lineColor;
        // Стандартный прямоугольник
        imagerectangle(...$args);

        $this->_save($im);
    }
}

/*class Triangle extends Shape
{
    public function __construct(array $params)
    {
        //...
    }
}*/


$shapes = [
    [
        'type' => 'circle',
        'params' => [
            'size' => [
                'width' => 200
            ],
            'line_color' => [213, 35, 220],
            'fill_color' => [0, 115, 0]
        ]
    ],
    [
        'type' => 'square',
        'params' => [
            'size' => [
                'width' => 150,
                'height' => 150
            ],
            'line_color' => [233, 215, 320],
            'fill_color' => [0, 55, 0]
        ]
    ]
];

DrawManager::getInstance()->register($shapes)->render();
