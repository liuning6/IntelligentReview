<?php
/**
* 图片相似度比较
*
* @version $Id: ImageHash.php 4429 2012-04-17 13:20:31Z jax $
* @author  jax.hu
*
* <code>
*  //Sample_1
*  $aHash = ImageHash::hashImageFile('wsz.11.jpg');
*  $bHash = ImageHash::hashImageFile('wsz.12.jpg');
*  var_dump(ImageHash::isHashSimilar($aHash, $bHash));
*
*  //Sample_2
*  var_dump(ImageHash::isImageFileSimilar('wsz.11.jpg', 'wsz.12.jpg'));
* </code>
*/

class ImageHash {
	
	/**取样倍率 1~10
	* @access public
	* @staticvar int
	* */
	function __construct($rate = 2){
		$this->rate = $rate;
	}

	/**相似度允许值 0~64
	* @access public
	* @staticvar int
	* */
	public static $similarity = 0;


	/**hash 图片
	* @param resource $src 图片 resource ID
	* @return string 图片 hash 值，失败则是 false
	* */
	function hashImage($src){
	if(!$src){ return false; }

	/*缩小图片尺寸*/
	$delta = 8 * $this->rate;//echo $this->rate;die;
	$img = imageCreateTrueColor($delta,$delta);
	imageCopyResized($img,$src, 0,0,0,0, $delta,$delta,imagesX($src),imagesY($src));

	/*计算图片灰阶值*/
	$grayArray = array();
	for ($y=0; $y<$delta; $y++){
	for ($x=0; $x<$delta; $x++){
	$rgb = imagecolorat($img,$x,$y);
	$col = imagecolorsforindex($img, $rgb);
	$gray = intval(($col['red']+$col['green']+$col['blue'])/3)& 0xFF;

	$grayArray[] = $gray;
	}
	}
	imagedestroy($img);

	/*计算所有像素的灰阶平均值*/
	$average = array_sum($grayArray)/count($grayArray);

	/*计算 hash 值*/
	$hashStr = '';
	foreach ($grayArray as $gray){
	$hashStr .= ($gray>=$average) ? '1' : '0';
	}

	return $hashStr;
	}


	/**hash 图片文件
	* @param string $filePath 文件地址路径
	* @return string 图片 hash 值，失败则是 false
	* */
	function hashImageFile($filePath){
	$src = imageCreateFromJPEG($filePath);
	$hashStr = self::hashImage($src);
	imagedestroy($src);

	return $hashStr;
	}


	/**比较两个 hash 值，是不是相似
	* @param string $aHash A图片的 hash 值
	* @param string $bHash B图片的 hash 值
	* @return bool 当图片相似则传递 true，否则是 false
	* */
	function isHashSimilar($aHash, $bHash){
	$aL = strlen($aHash); $bL = strlen($bHash);
	if ($aL !== $bL){
		return 0;
	}

	/*计算容许落差的数量*/
	//$allowGap = $aL*(100-self::$similarity)/100;

	/*计算两个 hash 值的汉明距离*/
	$distance = 0;
	for($i=0; $i<$aL; $i++){
	if ($aHash{$i} !== $bHash{$i}){ $distance++; }
	}

	//return ($distance<=$allowGap) ? true : false;
	//$j = ($distance<=$allowGap) ? '相似' : '非相似';
		//return '结果:' . $j . '<br />distance:' . $distance . '<br />allowGap:' . $allowGap . '<br /> aL:' . $aL . '<br />bL:' . $bL . '<br />相似度：'. ($allowGap - $distance) / $allowGap;
		return ($aL - $distance) / $aL * 100;
	}


	/**比较两个图片文件，是不是相似
	* @param string $aHash A图片的路径
	* @param string $bHash B图片的路径
	* @return bool 当图片相似则传递 true，否则是 false
	* */
	function isImageFileSimilar($aPath, $bPath){
	$aHash = $this->hashImageFile($aPath);
	$bHash = $this->hashImageFile($bPath);
	return $this->isHashSimilar($aHash, $bHash);
	}

}

//$IS = new ImageHash;

//echo $IS->isImageFileSimilar('x/1.jpg', 'x/2.jpg');
