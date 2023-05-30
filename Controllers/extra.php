<?php
class Extra
{
	public function loadImage($NeedSex, $CategoryID, $image)
	{
		switch($NeedSex){
				case '1':
					   $ml = 'm';
					break;
					case '2':
					   $ml = 'f';
					break;
					default:
					   $ml = 'f';
					break;		
				}	
				switch($CategoryID)
				{
					case 1:
						$link = 'equip/'.$ml.'/head/'.$image.'/icon_1.png?lv=semcache';
					break;
					case 2:
						$link = 'equip/'.$ml.'/glass/'.$image.'/icon_1.png?lv=semcache';
					break;
					case 3:
						$link = 'equip/'.$ml.'/hair/'.$image.'/icon_1.png?lv=semcache';
					break;
					case 5:
						$link = 'equip/'.$ml.'/cloth/'.$image.'/icon_1.png?lv=semcache';
					break;
					case 6:
						$link = 'equip/'.$ml.'/face/'.$image.'/icon_1.png?lv=semcache';
					break;
					case 7:
						$link = 'arm/'.$image.'/00.png?lv=semcache';
					break;
					case 8:
						$link = 'equip/armlet/'.$image.'/icon.png?lv=semcache';
					break;
					case 9:
						$link = 'equip/ring/'.$image.'/icon.png?lv=semcache';
					break;
					case 11:
						$link = 'unfrightprop/'.$image.'/icon.png?lv=semcache';
					break;
					case 13:
						$link = 'equip/'.$ml.'/suits/'.$image.'/icon_1.png?lv=semcache';
					break;
					case 15:
						$link = 'equip/wing/'.$image.'/icon.png?lv=semcache';
					break;
					case 14:
						$link = 'necklace/'.$image.'/icon.png?lv=semcache';
					break;
					case 17;
						$link = 'equip/offhand/'.$image.'/icon.png?lv=semcache';
					break;
					case 16;
						$link = 'specialprop/chatBall/'.$image.'/icon.png?lv=semcache';
					break;
					case 19;
						$link = 'prop/'.$image.'/icon.png?lv=semcache';
					break;
					case 20;
						$link = 'prop/'.$image.'/icon.png?lv=semcache';
					break;
					case 35;
						$link = 'unfrightprop/'.$image.'/icon.png?lv=semcache';
					break;
					case 34;
						$link = 'unfrightprop/'.$image.'/icon.png?lv=semcache';
					break;
					case 50;
						$link = 'petequip/arm/'.$image.'/icon.png?lv=semcache';
					break;
					case 52;
						$link = 'petequip/cloth/'.$image.'/icon.png?lv=semcache';
					break;
					case 51;
						$link = 'petequip/hat/'.$image.'/icon.png?lv=semcache';
					break;
					case 24;
				    $link = 'unfrightprop/'.$image.'/icon.png?lv=semcache';
					break;
					default:
						$link = NULL;
					break;
			}
		return $link;
	}
}
?>