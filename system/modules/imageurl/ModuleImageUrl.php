<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    ImageURL
 * @license    GNU/LGPL  
 * @filesource
 */

 
class ModuleImageUrl extends Controller {


	/**
    * ModuleImageUrl::replaceInsertTags
    *
    * Replaces the image_url insert-tag
    * @param string
    * @return string
    */
	protected function replaceInsertTags( $strBuffer, $blnCache=false ) {

        $aParams = explode('::', $strBuffer);

        switch( $aParams[0] ) {

            case 'image_url' :

				$width = null;
				$height = null;
				$alt = '';
				$class = '';
				$rel = '';
				$strFile = $aParams[1];
				$mode = '';

				// Take arguments
				if( strpos($aParams[1], '?') !== false ) {

					$this->import('String');

					$arrChunks = explode('?', urldecode($aParams[1]), 2);
					$strSource = $this->String->decodeEntities($arrChunks[1]);
					$strSource = str_replace('[&]', '&', $strSource);
					$arrParams = explode('&', $strSource);

					foreach( $arrParams as $strParam ) {

						list($key, $value) = explode('=', $strParam);

						switch( $key ) {
							case 'width':
								$width = $value;
							break;

							case 'height':
								$height = $value;
							break;

							case 'alt':
								$alt = specialchars($value);
							break;

							case 'class':
								$class = $value;
							break;

							case 'rel':
								$rel = $value;
							break;

							case 'mode':
								$mode = $value;
							break;
						}
					}

					$strFile = $arrChunks[0];
				}

				// Sanitize path
				$strFile = str_replace('../', '', $strFile);

				// Check maximum image width
				if( $GLOBALS['TL_CONFIG']['maxImageWidth'] > 0 && $width > $GLOBALS['TL_CONFIG']['maxImageWidth'] ) {
					$width = $GLOBALS['TL_CONFIG']['maxImageWidth'];
					$height = null;
				}

				// Generate the thumbnail image
				try {

					$src = $this->getImage($strFile, $width, $height, $mode);
					$dimensions = '';

					// Add the image dimensions
					if( ($imgSize = @getimagesize(TL_ROOT .'/'. $src)) !== false ) {
						$dimensions = $imgSize[3];
					}

					return $src;

				} catch( Exception $e ) {
					return '';
				}
            break;

            default :
                return false;
            break;
        }

        return false;
    
    }
}

?>