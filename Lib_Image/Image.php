<?php
/**
* 图片文件处理库（需要PHP环境安装有Imagick扩展）
* 说明：完美兼容gif动画、jpg等图片
*		如有任何错误，请指正！
* @version 1.0(2012-11-17)
* @author WiFeng(admin@521-wf.com)
* @link http://521-wf.com
*/

class Image {
	private $oriImage; //原始图片
	private $desImage; //生成图片
	private $imgType; //图片类型

	const DRAWTYPE_SCALE		= 1;
	const DRAWTYPE_FILL			= 2;
	const DRAWTYPE_SCALE_FILL	= 3;

	const FITPOS_LEFT			= 1;
	const FITPOS_RIGHT			= 2;
	const FITPOS_TOP			= 3;
	const FITPOS_BOTTOM			= 4;
	const FITPOS_LEFT_TOP		= 5;
	const FITPOS_LEFT_BOTTOM	= 6;
	const FITPOS_RIGHT_TOP		= 7;
	const FITPOS_RIGHT_BOTTOM	= 8;
	const FITPOS_CENTER	= 9;

	public function __construct($oriFilename) {
		$this->Image($oriFilename);
	}
	
	/**
	* @param string $oriFilename 原始图片路径
	*/
	public function Image($oriFilename) {
		$this->oriImage = $this->desImage = new Imagick($oriFilename);
		$this->desImage = new Imagick();
		$this->imgType = strtolower($this->oriImage->getImageFormat());
	}
	

	/**
	* 图片裁减
	*/
	public function crop($x = 0, $y = 0, $width = NULL, $height = NULL) {
		if($width === NULL) {
			$width = $this->oriImage->getImageWidth()-$x;
		}
	    if($height === NULL) {
			$height = $this->oriImage->getImageHeight()-$y;
		}
	    if($width < 1 || $height < 1) {
			return NULL;
	    }

	    if($this->imgType=='gif') {	        
        	$images = $this->oriImage->coalesceImages();
    	    foreach($images as $frame){
    	        $img = new Imagick();
    	        $img->readImageBlob($frame);
                $img->cropImage($width, $height, $x, $y);
				
                $this->desImage->addImage($img);
                $this->desImage->setImageDelay($img->getImageDelay());
                $this->desImage->setImagePage($width, $height, 0, 0);
            }
            $this->oriImage->destroy();
	    } else {
	        $this->oriImage->cropImage($width, $height, $x, $y);
			$this->desImage = $this->oriImage;
	    }
	}

	/**
	* 调整图片大小
	* @param int $width
	* @param int $height
	* @param string $drawType 渲染图像方式 
	*	DRAWTYPE_SCALE 按照图片实际宽高比例调整
	*	DRAWTYPE_FILL 按照指定宽高调整
	*	DRAWTYPE_SCALE_FILL 按照图片实际宽高比例调整，空白区域使用设置的色彩与位置进行处理
	* @param string $fitPosition 真实图片在调整后的方位(在$drawType=DRAWTYPE_SCALE_FILL时有效)
	*	(top | bottom | left | right | left_top, left_bottom, right_top, right_bottom)
	* @param array $fillBgcolor 填充背景色彩(在$drawType=DRAWTYPE_SCALE_FILL时有效， array(R, G, B)
	*/
	public function resize($width, $height, $drawType = self::DRAWTYPE_SCALE, $fitPos = self::FITPOS_CENTER,  $fillBgcolor = array(255,255,255)) {
		$imagePage = $this->oriImage->getImagePage();
		$img_width = $imagePage['width'];
		$img_height = $imagePage['height'];

		$fit = true;
		$crop_x = 0;
		$crop_y = 0;
		$crop_width = $img_width;
		$crop_height = $img_height;
		$color = 'rgb(0,0,255)';

		switch($drawType) {
			case self::DRAWTYPE_SCALE :
				$fit = true;
				break;
			case self::DRAWTYPE_FILL :
				$fit = false; 
				$crop_width = $width;
				$crop_height = $height;
				break;
			case self::DRAWTYPE_SCALE_FILL :
				$fit = true;
				$color = 'rgb('.$fillBgcolor[0] .','. $fillBgcolor[1] .','. $fillBgcolor[2].')';
				if($width < $img_width || $height < $img_height) {
					$p = 1;
					$p1 = $width / $img_width;
					$p2 = $height / $img_height;
					$p = $p1 > $p2 ? $p2 : $p1;
					$crop_width = floor($img_width * $p);
					$crop_height = floor($img_height* $p);
				} 
				switch($fitPos) {
					case self::FITPOS_CENTER :
						$crop_x = floor(($width - $crop_width) / 2);
						$crop_y = floor(($height - $crop_height) / 2);
						break;
					case self::FITPOS_LEFT : 
						$crop_y = floor(($height - $crop_height) / 2);
						break;
					case self::FITPOS_RIGHT :
						$crop_x = $width - $crop_width;
						$crop_y = floor(($height - $crop_height) / 2);
						break;
					case self::FITPOS_TOP :
						$crop_x = floor(($width - $crop_width) / 2);
						break;
					case self::FITPOS_BOTTOM :
						$crop_x = floor(($width - $crop_width) / 2);
						$crop_y = $height - $crop_height;
						break;
					case self::FITPOS_LEFT_TOP :
						break;
					case self::FITPOS_LEFT_BOTTOM :
						$crop_y = $height - $crop_height;
						break;
					case self::FITPOS_RIGHT_TOP :
						$crop_x = $width - $crop_width;
						break;
					case self::FITPOS_RIGHT_BOTTOM :
						$crop_x = $width - $crop_width;
						$crop_y = $height - $crop_height;
						break;
				}
			
				break;
		}
		
		if($this->imgType == 'gif') {
			$images = $this->oriImage->coalesceImages();
			foreach($images as $frame) {
				$img = new Imagick();
				$img->readImageBlob($frame);
				$img->thumbnailImage($crop_width, $crop_height, $fit);
				if($drawType == self::DRAWTYPE_SCALE_FILL) {
					$draw = new ImagickDraw();
					$draw->composite(Imagick::COMPOSITE_OVER, $crop_x, $crop_y, $crop_width, $crop_height, $img);
					$im = new Imagick();
					$im->newImage($width, $height, new ImagickPixel($color), $this->imgType);					
                    $im->drawImage($draw);
				}
				$this->desImage->addImage($im);
				$this->desImage->setImageDelay($img->getImageDelay());
			}
			$this->oriImage->destroy();
		} else {
			$this->oriImage->thumbnailImage($width, $height, $fit);
			if($drawType == self::DRAWTYPE_SCALE_FILL) {
				$draw = new ImagickDraw();
				$draw->composite(Imagick::COMPOSITE_OVER, $crop_x, $crop_y, $crop_width, $crop_height, $this->oriImage);
				$im = new Imagick();
				$im->newImage($width, $height, new ImagickPixel($color), $this->imgType);
				$im->drawImage($draw);
			}
			$this->desImage = $im;
		}
	}
	
	/**
	* 直接输出
	*/
	public function output() {
		header('Content-type: '.$this->imgType);
		echo $this->desImage->getImagesBlob();
	}

	/**
	* 保存图片
	* @param string $filename 要生成的图片路径
	* @return boolean
	*/
	public function save($filename, $replace = false) {
		if(file_exists($filename)) {
			if(!$replace) {
				echo "The File [$filename] is exists!";
				return NULL;
			}
			unlink($filename);
		}
		if($this->imgType == 'gif') {
			$filename .= '.gif';
	        $flag = $this->desImage->writeImages($filename, true);
			return $flag && rename($filename, substr($filename, 0, -4));
	    } else {
	        return $this->desImage->writeImage($filename);
	    }
	}

	/**
	* 释放资源
	*/
	public function destroy() {
		$this->oriImage->destroy();
		$this->desImage->destroy();
	}
}

?>