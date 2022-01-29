<?php

/**
 * Theme Warlock - Google_Fonts
 * 
 * @title      Google Fonts
 * @desc       Holds definitions of Google Fonts
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Google_Fonts {
    
    // Font definition elements
    const FONT_FAMILY = 'font-family';
    const FONT_STYLES = 'font-styles';
    
    // Font styles
    const FONT_STYLE_100        = '100';
    const FONT_STYLE_200        = '200';
    const FONT_STYLE_300        = '300';
    const FONT_STYLE_400        = '400';
    const FONT_STYLE_500        = '500';
    const FONT_STYLE_600        = '600';
    const FONT_STYLE_700        = '700';
    const FONT_STYLE_800        = '800';
    const FONT_STYLE_900        = '900';
    const FONT_STYLE_100_ITALIC = '100italic';
    const FONT_STYLE_200_ITALIC = '200italic';
    const FONT_STYLE_300_ITALIC = '300italic';
    const FONT_STYLE_400_ITALIC = '400italic';
    const FONT_STYLE_500_ITALIC = '500italic';
    const FONT_STYLE_600_ITALIC = '600italic';
    const FONT_STYLE_700_ITALIC = '700italic';
    const FONT_STYLE_800_ITALIC = '800italic';
    const FONT_STYLE_900_ITALIC = '900italic';
    
    /**
     * List of available Google Fonts
     */
    const FONT_LIST = array(
        array(self::FONT_FAMILY => 'ABeeZee', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Abel', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Abhaya Libre', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Abril Fatface', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Aclonica', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Acme', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Actor', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Adamina', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Advent Pro', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Aguafina Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Akronim', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Aladin', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Alata', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Alatsi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Aldrich', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Alef', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Alegreya', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Alegreya SC', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Alegreya Sans', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Alegreya Sans SC', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Aleo', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Alex Brush', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Alfa Slab One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Alice', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Alike', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Alike Angular', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Allan', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Allerta', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Allerta Stencil', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Allura', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Almarai', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Almendra', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Almendra Display', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Almendra SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Amarante', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Amaranth', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Amatic SC', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Amethysta', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Amiko', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Amiri', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Amita', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Anaheim', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Andada', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Andika', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Angkor', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Annie Use Your Telescope', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Anonymous Pro', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Antic', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Antic Didone', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Antic Slab', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Anton', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Arapey', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Arbutus', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Arbutus Slab', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Architects Daughter', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Archivo', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Archivo Black', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Archivo Narrow', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Aref Ruqaa', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Arima Madurai', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Arimo', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Arizonia', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Armata', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Arsenal', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Artifika', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Arvo', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Arya', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Asap', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Asap Condensed', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Asar', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Asset', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Assistant', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Astloch', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Asul', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Athiti', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Atma', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Atomic Age', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Aubrey', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Audiowide', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Autour One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Average', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Average Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Averia Gruesa Libre', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Averia Libre', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Averia Sans Libre', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Averia Serif Libre', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'B612', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'B612 Mono', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Bad Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bahiana', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bahianita', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bai Jamjuree', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Baloo 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Bhai 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Bhaina 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Chettan 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Da 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Paaji 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Tamma 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Tammudu 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Baloo Thambi 2', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Balsamiq Sans', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Balthazar', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bangers', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Barlow', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Barlow Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Barlow Semi Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Barriecito', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Barrio', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Basic', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Baskervville', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Battambang', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Baumans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bayon', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Be Vietnam', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Bebas Neue', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Belgrano', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bellefair', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Belleza', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bellota', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Bellota Text', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'BenchNine', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Bentham', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Berkshire Swash', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Beth Ellen', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bevan', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Big Shoulders Display', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Big Shoulders Text', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Bigelow Rules', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bigshot One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bilbo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bilbo Swash Caps', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'BioRhyme', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'BioRhyme Expanded', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Biryani', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Bitter', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Black And White Picture', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Black Han Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Black Ops One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Blinker', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Bokor', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bonbon', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Boogaloo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bowlby One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bowlby One SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Brawler', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bree Serif', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bubblegum Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bubbler One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Buda', self::FONT_STYLES => array(self::FONT_STYLE_300)), 
        array(self::FONT_FAMILY => 'Buenard', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Bungee', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bungee Hairline', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bungee Inline', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bungee Outline', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Bungee Shade', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Butcherman', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Butterfly Kids', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cabin', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cabin Condensed', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cabin Sketch', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Caesar Dressing', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cagliostro', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cairo', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Caladea', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Calistoga', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Calligraffitti', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cambay', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cambo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Candal', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cantarell', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cantata One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cantora One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Capriola', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cardo', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Carme', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Carrois Gothic', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Carrois Gothic SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Carter One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Catamaran', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Caudex', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Caveat', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Caveat Brush', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cedarville Cursive', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ceviche One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chakra Petch', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Changa', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Changa One', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chango', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Charm', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Charmonman', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Chathura', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Chau Philomene One', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chela One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chelsea Market', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chenla', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cherry Cream Soda', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cherry Swash', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Chewy', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chicle', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chilanka', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Chivo', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Chonburi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cinzel', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Cinzel Decorative', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Clicker Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Coda', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Coda Caption', self::FONT_STYLES => array(self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Codystar', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Coiny', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Combo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Comfortaa', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Comic Neue', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Coming Soon', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Concert One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Condiment', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Content', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Contrail One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Convergence', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cookie', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Copse', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Corben', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cormorant', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cormorant Garamond', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cormorant Infant', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cormorant SC', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cormorant Unicase', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cormorant Upright', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Courgette', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Courier Prime', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cousine', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Coustard', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Covered By Your Grace', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Crafty Girls', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Creepster', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Crete Round', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Crimson Pro', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Crimson Text', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Croissant One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Crushed', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cuprum', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Cute Font', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cutive', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Cutive Mono', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'DM Mono', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500)), 
        array(self::FONT_FAMILY => 'DM Sans', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'DM Serif Display', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'DM Serif Text', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Damion', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Dancing Script', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Dangrek', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Darker Grotesque', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'David Libre', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Dawning of a New Day', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Days One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Dekko', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Delius', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Delius Swash Caps', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Delius Unicase', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Della Respira', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Denk One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Devonshire', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Dhurjati', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Didact Gothic', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Diplomata', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Diplomata SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Do Hyeon', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Dokdo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Domine', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Donegal One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Doppio One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Dorsa', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Dosis', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Dr Sugiyama', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Duru Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Dynalight', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'EB Garamond', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Eagle Lake', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'East Sea Dokdo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Eater', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Economica', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Eczar', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'El Messiri', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Electrolize', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Elsie', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Elsie Swash Caps', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Emblema One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Emilys Candy', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Encode Sans', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Encode Sans Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Encode Sans Expanded', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Encode Sans Semi Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Encode Sans Semi Expanded', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Engagement', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Englebert', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Enriqueta', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Epilogue', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Erica One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Esteban', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Euphoria Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ewert', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Exo', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Exo 2', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Expletus Sans', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Fahkwang', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Fanwood Text', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Farro', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Farsan', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fascinate', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fascinate Inline', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Faster One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fasthand', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fauna One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Faustina', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Federant', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Federo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Felipa', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fenix', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Finger Paint', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fira Code', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Fira Mono', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Fira Sans', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Fira Sans Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Fira Sans Extra Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Fjalla One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fjord One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Flamenco', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Flavors', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fondamento', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fontdiner Swanky', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Forum', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Francois One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Frank Ruhl Libre', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Freckle Face', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fredericka the Great', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fredoka One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Freehand', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fresca', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Frijole', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fruktur', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Fugaz One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'GFS Didot', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'GFS Neohellenic', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gabriela', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gaegu', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gafata', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Galada', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Galdeano', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Galindo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gamja Flower', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gayathri', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gelasio', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gentium Basic', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gentium Book Basic', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Geo', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Geostar', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Geostar Fill', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Germania One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gidugu', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gilda Display', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Girassol', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Give You Glory', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Glass Antiqua', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Glegoo', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gloria Hallelujah', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Goblin One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gochi Hand', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gorditas', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gothic A1', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Gotu', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Goudy Bookletter 1911', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Graduate', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Grand Hotel', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Grandstander', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Gravitas One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Great Vibes', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Grenze', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Grenze Gotisch', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Griffy', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gruppo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gudea', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gugi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Gupter', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Gurajada', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Habibi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Halant', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Hammersmith One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Hanalei', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Hanalei Fill', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Handlee', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Hanuman', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Happy Monkey', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Harmattan', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Headland One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Heebo', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Henny Penny', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Hepta Slab', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Herr Von Muellerhoff', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Hi Melody', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Hind', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Hind Guntur', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Hind Madurai', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Hind Siliguri', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Hind Vadodara', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Holtwood One SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Homemade Apple', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Homenaje', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IBM Plex Mono', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'IBM Plex Sans', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'IBM Plex Sans Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'IBM Plex Serif', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'IM Fell DW Pica', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell DW Pica SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell Double Pica', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell Double Pica SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell English', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell English SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell French Canon', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell French Canon SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell Great Primer', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'IM Fell Great Primer SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ibarra Real Nova', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Iceberg', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Iceland', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Imprima', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Inconsolata', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Inder', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Indie Flower', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Inika', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Inknut Antiqua', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Inria Sans', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Inria Serif', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Inter', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Irish Grover', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Istok Web', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Italiana', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Italianno', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Itim', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jacques Francois', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jacques Francois Shadow', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jaldi', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Jim Nightshade', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jockey One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jolly Lodger', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jomhuria', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jomolhari', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Josefin Sans', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Josefin Slab', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Jost', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Joti One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jua', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Judson', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Julee', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Julius Sans One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Junge', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Jura', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Just Another Hand', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Just Me Again Down Here', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'K2D', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Kadwa', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kalam', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kameron', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kanit', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Kantumruy', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Karla', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Karma', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Katibeh', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kaushan Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kavivanar', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kavoon', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kdam Thmor', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Keania One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kelly Slab', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kenia', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Khand', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Khmer', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Khula', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Kirang Haerang', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kite One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Knewave', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'KoHo', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kodchasan', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kosugi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kosugi Maru', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kotta One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Koulen', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kranky', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kreon', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kristi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Krona One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Krub', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kufam', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Kulim Park', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kumar One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kumar One Outline', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Kumbh Sans', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Kurale', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'La Belle Aurore', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lacquer', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Laila', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Lakki Reddy', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lalezar', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lancelot', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lateef', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lato', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'League Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Leckerli One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ledger', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lekton', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Lemon', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lemonada', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Lexend Deca', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lexend Exa', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lexend Giga', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lexend Mega', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lexend Peta', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lexend Tera', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lexend Zetta', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Barcode 128', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Barcode 128 Text', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Barcode 39', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Barcode 39 Extended', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Barcode 39 Extended Text', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Barcode 39 Text', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Baskerville', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Libre Caslon Display', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Libre Caslon Text', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Libre Franklin', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Life Savers', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Lilita One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lily Script One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Limelight', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Linden Hill', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Literata', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Liu Jian Mao Cao', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Livvic', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Lobster', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lobster Two', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Londrina Outline', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Londrina Shadow', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Londrina Sketch', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Londrina Solid', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Long Cang', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lora', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Love Ya Like A Sister', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Loved by the King', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lovers Quarrel', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Luckiest Guy', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Lusitana', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Lustria', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'M PLUS 1p', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'M PLUS Rounded 1c', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Ma Shan Zheng', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Macondo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Macondo Swash Caps', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mada', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Magra', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Maiden Orange', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Maitree', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Major Mono Display', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mako', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mali', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Mallanna', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mandali', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Manjari', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Manrope', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Mansalva', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Manuale', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Marcellus', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Marcellus SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Marck Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Margarine', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Markazi Text', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Marko One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Marmelad', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Martel', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Martel Sans', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Marvel', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Mate', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mate SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Material Icons', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Maven Pro', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'McLaren', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Meddon', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'MedievalSharp', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Medula One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Meera Inimai', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Megrim', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Meie Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Merienda', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Merienda One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Merriweather', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Merriweather Sans', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Metal', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Metal Mania', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Metamorphous', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Metrophobic', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Michroma', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Milonga', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Miltonian', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Miltonian Tattoo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mina', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Miniver', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Miriam Libre', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Mirza', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Miss Fajardose', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mitr', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Modak', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Modern Antiqua', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mogra', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Molengo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Molle', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC)), 
        array(self::FONT_FAMILY => 'Monda', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Monofett', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Monoton', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Monsieur La Doulaise', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Montaga', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Montez', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Montserrat', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Montserrat Alternates', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Montserrat Subrayada', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Moul', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Moulpali', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mountains of Christmas', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Mouse Memoirs', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mr Bedfort', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mr Dafoe', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mr De Haviland', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mrs Saint Delafield', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mrs Sheppards', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Mukta', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Mukta Mahee', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Mukta Malar', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Mukta Vaani', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Mulish', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'MuseoModerno', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Mystery Quest', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'NTR', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nanum Brush Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nanum Gothic', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Nanum Gothic Coding', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Nanum Myeongjo', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Nanum Pen Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Neucha', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Neuton', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'New Rocker', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'News Cycle', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Niconne', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Niramit', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Nixie One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nobile', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Nokora', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Norican', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nosifer', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Notable', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nothing You Could Do', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Noticia Text', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Noto Sans', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Noto Sans HK', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Sans JP', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Sans KR', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Sans SC', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Sans TC', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Serif', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Noto Serif JP', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Serif KR', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Serif SC', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Noto Serif TC', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Nova Cut', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nova Flat', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nova Mono', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nova Oval', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nova Round', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nova Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nova Slim', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nova Square', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Numans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Nunito', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Nunito Sans', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Odibee Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Odor Mean Chey', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Offside', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Old Standard TT', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Oldenburg', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Oleo Script', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Oleo Script Swash Caps', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Open Sans', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Open Sans Condensed', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Oranienbaum', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Orbitron', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Oregano', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Orienta', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Original Surfer', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Oswald', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Over the Rainbow', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Overlock', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Overlock SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Overpass', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Overpass Mono', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Ovo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Oxanium', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Oxygen', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Oxygen Mono', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'PT Mono', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'PT Sans', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'PT Sans Caption', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'PT Sans Narrow', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'PT Serif', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'PT Serif Caption', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pacifico', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Padauk', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Palanquin', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Palanquin Dark', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Pangolin', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Paprika', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Parisienne', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Passero One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Passion One', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Pathway Gothic One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Patrick Hand', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Patrick Hand SC', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pattaya', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Patua One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pavanam', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Paytone One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Peddana', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Peralta', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Permanent Marker', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Petit Formal Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Petrona', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Philosopher', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Piazzolla', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Piedra', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pinyon Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pirata One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Plaster', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Play', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Playball', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Playfair Display', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Playfair Display SC', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Podkova', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Poiret One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Poller One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Poly', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pompiere', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pontano Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Poor Story', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Poppins', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Port Lligat Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Port Lligat Slab', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pragati Narrow', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Prata', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Preahvihear', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Press Start 2P', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Pridi', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Princess Sofia', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Prociono', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Prompt', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Prosto One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Proza Libre', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Public Sans', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Puritan', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Purple Purse', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Quando', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Quantico', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Quattrocento', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Quattrocento Sans', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Questrial', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Quicksand', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Quintessential', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Qwigley', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Racing Sans One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Radley', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rajdhani', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Rakkas', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Raleway', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Raleway Dots', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ramabhadra', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ramaraja', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rambla', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Rammetto One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ranchers', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rancho', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ranga', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Rasa', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Rationale', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ravi Prakash', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Recursive', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Red Hat Display', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Red Hat Text', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Red Rose', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Redressed', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Reem Kufi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Reenie Beanie', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Revalia', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rhodium Libre', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ribeye', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ribeye Marrow', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Righteous', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Risque', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Roboto', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Roboto Condensed', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Roboto Mono', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Roboto Slab', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Rochester', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rock Salt', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rokkitt', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Romanesco', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ropa Sans', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rosario', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Rosarivo', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rouge Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rowdies', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Rozha One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rubik', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Rubik Mono One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ruda', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Rufina', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Ruge Boogie', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ruluko', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rum Raisin', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ruslan Display', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Russo One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ruthie', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Rye', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sacramento', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sahitya', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Sail', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Saira', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Saira Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Saira Extra Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Saira Semi Condensed', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Saira Stencil One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Salsa', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sanchez', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sancreek', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sansita', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Sansita Swashed', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Sarabun', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Sarala', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Sarina', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sarpanch', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Satisfy', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sawarabi Gothic', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sawarabi Mincho', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Scada', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Scheherazade', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Schoolbell', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Scope One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Seaweed Script', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Secular One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sedgwick Ave', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sedgwick Ave Display', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sen', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Sevillana', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Seymour One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Shadows Into Light', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Shadows Into Light Two', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Shanti', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Share', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Share Tech', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Share Tech Mono', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Shojumaru', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Short Stack', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Shrikhand', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Siemreap', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sigmar One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Signika', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Signika Negative', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Simonetta', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Single Day', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sintony', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Sirin Stencil', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Six Caps', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Skranji', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Slabo 13px', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Slabo 27px', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Slackey', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Smokum', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Smythe', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sniglet', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Snippet', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Snowburst One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sofadi One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sofia', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Solway', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Song Myung', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sonsie One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sora', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Sorts Mill Goudy', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Source Code Pro', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Source Sans Pro', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Source Serif Pro', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Space Mono', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Spartan', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Special Elite', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Spectral', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Spectral SC', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Spicy Rice', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Spinnaker', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Spirax', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Squada One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sree Krushnadevaraya', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sriracha', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Srisakdi', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Staatliches', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Stalemate', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Stalinist One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Stardos Stencil', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Stint Ultra Condensed', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Stint Ultra Expanded', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Stoke', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Strait', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Stylish', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sue Ellen Francisco', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Suez One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sulphur Point', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Sumana', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Sunflower', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Sunshiney', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Supermercado One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Sura', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Suranna', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Suravaram', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Suwannaphum', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Swanky and Moo Moo', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Syncopate', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Syne', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Tajawal', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Tangerine', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Taprom', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Tauri', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Taviraj', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Teko', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Telex', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Tenali Ramakrishna', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Tenor Sans', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Text Me One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Thasadith', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'The Girl Next Door', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Tienne', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Tillana', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Timmana', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Tinos', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Titan One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Titillium Web', self::FONT_STYLES => array(self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Tomorrow', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Trade Winds', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Trirong', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Trocchi', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Trochut', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Trykker', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Tulpen One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Turret Road', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_800)), 
        array(self::FONT_FAMILY => 'Ubuntu', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Ubuntu Condensed', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Ubuntu Mono', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Ultra', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Uncial Antiqua', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Underdog', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Unica One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'UnifrakturCook', self::FONT_STYLES => array(self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'UnifrakturMaguntia', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Unkempt', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Unlock', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Unna', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'VT323', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Vampiro One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Varela', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Varela Round', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Varta', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Vast Shadow', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Vesper Libre', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Viaoda Libre', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Vibes', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Vibur', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Vidaloka', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Viga', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Voces', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Volkhov', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Vollkorn', self::FONT_STYLES => array(self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Vollkorn SC', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Voltaire', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Waiting for the Sunrise', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Wallpoet', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Walter Turncoat', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Warnes', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Wellfleet', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Wendy One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Wire One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Work Sans', self::FONT_STYLES => array(self::FONT_STYLE_100_ITALIC, self::FONT_STYLE_200_ITALIC, self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_800_ITALIC, self::FONT_STYLE_900_ITALIC, self::FONT_STYLE_100, self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700, self::FONT_STYLE_800, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Yanone Kaffeesatz', self::FONT_STYLES => array(self::FONT_STYLE_200, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Yantramanav', self::FONT_STYLES => array(self::FONT_STYLE_100, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_700, self::FONT_STYLE_900)), 
        array(self::FONT_FAMILY => 'Yatra One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Yellowtail', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Yeon Sung', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Yeseva One', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Yesteryear', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Yrsa', self::FONT_STYLES => array(self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'ZCOOL KuaiLe', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'ZCOOL QingKe HuangYou', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'ZCOOL XiaoWei', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Zeyada', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Zhi Mang Xing', self::FONT_STYLES => array(self::FONT_STYLE_400)), 
        array(self::FONT_FAMILY => 'Zilla Slab', self::FONT_STYLES => array(self::FONT_STYLE_300_ITALIC, self::FONT_STYLE_400_ITALIC, self::FONT_STYLE_500_ITALIC, self::FONT_STYLE_600_ITALIC, self::FONT_STYLE_700_ITALIC, self::FONT_STYLE_300, self::FONT_STYLE_400, self::FONT_STYLE_500, self::FONT_STYLE_600, self::FONT_STYLE_700)), 
        array(self::FONT_FAMILY => 'Zilla Slab Highlight', self::FONT_STYLES => array(self::FONT_STYLE_400, self::FONT_STYLE_700)), 
    );

    /**
     * Stored weights for each font class
     *
     * @var array
     */
    protected static $_storedWeights = array();
    
    /**
     * Get the stored weights for each font class, matching the closest weight
     * available for the provided font family
     * 
     * @param string $fontClass  Font Class
     * @param string $fontFamily Font Family
     * @return string[] Numeric weight strings, <b>100</b>...<b>900</b>, <b>100italic</b>...<b>900italic</b>
     * @throws Exception
     */
    public static function getWeights($fontClass, $fontFamily) {
        // Get the stored weights
        $storedWeights = isset(self::$_storedWeights[$fontClass]) ? self::$_storedWeights[$fontClass] : array(self::FONT_STYLE_400);
        
        // Find the font family
        $fontFamilyDetails = null;
        foreach (self::FONT_LIST as $fontListItem) {
            // Found our match
            if ($fontFamily == $fontListItem[self::FONT_FAMILY]) {
                $fontFamilyDetails = $fontListItem;
                break;
            }
        }
        
        // Invalid font
        if (!is_array($fontFamilyDetails)) {
            throw new Exception('Font family "' . $fontFamily . '" not found!');
        }
        
        // Prepare the search matrixes
        $fontWeights = array(
            'normal' => array(),
            'italic' => array(),
        );
        foreach ($fontFamilyDetails[self::FONT_STYLES] as $fontStyle) {
            if (preg_match('%italic$%', $fontStyle)) {
                $fontWeights['italic'][] = intval(preg_replace('%italic$%', '', $fontStyle));
            } else {
                $fontWeights['normal'][] = intval($fontStyle);
            }
        }
        
        // Prepare the weight matches
        $weightMatches = array();
        
        // Go trough our request list
        foreach ($storedWeights as $storedWeight) {
            // Prepare the search matrix
            $searchMatrix = preg_match('%italic$%', $storedWeight) && count($fontWeights['italic']) ? 'italic' : 'normal';
            
            // Get the weight as integer
            $storedWeightInt = intval(preg_replace('%italic$%', '', $storedWeight));
            
            // Prepare the distances
            $storedWeightDistances = array();
            foreach ($fontWeights[$searchMatrix] as $searchMatrixWeight) {
                $storedWeightDistances[$searchMatrixWeight] = abs($searchMatrixWeight - $storedWeightInt);
            }
            
            // Sort the distances
            asort($storedWeightDistances);
            
            // Store the weight match
            $weightMatches[] = strval(current(array_keys($storedWeightDistances))) . ('normal' === $searchMatrix ? '' : 'italic');
        }
        
        // Unique values
        return array_values(array_unique($weightMatches));
    }
    
    /**
     * Append the required font weights, as they were declared in 
     * <b><f-x></b> calls in inline CSS.
     * 
     * @param string $fontClass  Font Class; example <b>h1</b>
     * @param array  $fontWeights (optional) Required font weights.<br/>
     * Allowed weights: <ul>
     * <li><b>100</b>...<b>900</b>, <b>100italic</b>...<b>900italic</b></li>
     * <li><b>light</b> (200), <b>light-italic</b> (200italic)</li>
     * <li><b>lighter</b> (100), <b>lighter-italic</b> (100italic)</li>
     * <li><b>bold</b> (800), <b>bold-italic</b> (800italic)</li>
     * <li><b>bolder</b> (900), <b>bolder-italic</b> (900italic)</li>
     * <li><b>normal</b> (400)</li>
     * <li><b>regular</b> (400)</li>
     * <li><b>italic</b> (400italic)</li>
     * </ul><br/>
     * default <b>empty array</b>
     */
    public static function storeWeight($fontClass, Array $fontWeights = array()) {
        // Initialize the storage
        if (!isset(self::$_storedWeights[$fontClass])) {
            self::$_storedWeights[$fontClass] = array();
        }
        
        // No weight found
        if (!count($fontWeights)) {
            $fontWeights = array(self::FONT_STYLE_400);
        }
        
        // Prepare the converted font weights
        $cleanedFontWeights = array();
        
        // Go through the user input
        foreach ($fontWeights as $fontWeight) {
            // Convert to string
            $fontWeight = strval($fontWeight);
            
            // Valid range or font weight
            if (preg_match('%^(?:[1-9]00|light(?:er)?(?:\-italic)?|normal|regular|italic|bold(?:er)?(?:\-italic)?)$%', $fontWeight)) {
                switch($fontWeight) {
                    case 'lighter':
                        $cleanedFontWeights[] = self::FONT_STYLE_100;
                        break;
                    
                    case 'lighter-italic':
                        $cleanedFontWeights[] = self::FONT_STYLE_100_ITALIC;
                        break;
                    
                    case 'light':
                        $cleanedFontWeights[] = self::FONT_STYLE_200;
                        break;
                    
                    case 'light-italic':
                        $cleanedFontWeights[] = self::FONT_STYLE_200_ITALIC;
                        break;
                    
                    case 'normal':
                    case 'regular':
                        $cleanedFontWeights[] = self::FONT_STYLE_400;
                        break;
                    
                    case 'italic':
                        $cleanedFontWeights[] = self::FONT_STYLE_400_ITALIC;
                        break;
                    
                    case 'bold':
                        $cleanedFontWeights[] = self::FONT_STYLE_800;
                        break;
                    
                    case 'bold-italic':
                        $cleanedFontWeights[] = self::FONT_STYLE_800_ITALIC;
                        break;
                    
                    case 'bolder':
                        $cleanedFontWeights[] = self::FONT_STYLE_900;
                        break;
                    
                    case 'bolder-italic':
                        $cleanedFontWeights[] = self::FONT_STYLE_900_ITALIC;
                        break;
                    
                    default:
                        // A weight in 100-900 range
                        $cleanedFontWeights[] = $fontWeight;
                        break;
                }
            } else {
                // Valid 100italic-900italic range
                if (preg_match('%^[1-9]00italic$%', $fontWeight)) {
                    $cleanedFontWeights[] = $fontWeight;
                } else {
                    // Revert to the deafult
                    $cleanedFontWeights[] = self::FONT_STYLE_400;
                }
            }
        }
        
        // Append the font weights
        if (count($cleanedFontWeights)) {
            self::$_storedWeights[$fontClass] = array_unique(
                array_merge(
                    self::$_storedWeights[$fontClass], 
                    $cleanedFontWeights
                )
            );
            
            // Sort the result
            natsort(self::$_storedWeights[$fontClass]);
            
            // Reset the keys
            self::$_storedWeights[$fontClass] = array_values(self::$_storedWeights[$fontClass]);
        }
    }
    
    /**
     * Get the available font families
     * 
     * @example ["Zeyada", "Exo"]
     * @return string[]
     */
    public static function getFontFamilies() {
        // Cache hit
        if (null !== $result = Cache::get()) {
            return $result;
        }
        
        // Prepare the result
        $result = array();
        foreach (self::FONT_LIST as $fontDetails) {
            $result[] = $fontDetails[self::FONT_FAMILY];
        }

        // Store in cache
        Cache::set($result);
        
        // All done
        return $result;
    }
}

/* EOF */