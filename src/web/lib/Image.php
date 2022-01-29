<?php

/**
 * Theme Warlock - Image
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Image {
    
    // Blending modes
    const BLEND_MODE_DESATURATE = 'desaturate';
    const BLEND_MODE_OVERLAY    = 'overlay';
    const BLEND_MODE_NORMAL     = 'normal';
    const BLEND_MODE_COLOR      = 'color';
    
    // Cache constants
    const CACHE_PREFIX_RGB_TO_HSL = 'rgbToHsl';
    const CACHE_PREFIX_HSL_TO_RGB = 'hslToRgb';
    const CACHE_PREFIX_COLORS_HSL = 'colorsHsl';
    
    /**
     * Array of recognized colors
     * 
     * @var array
     */
    public static $colors = array(
        'acid green' => array (143, 254, 9),
        'algae' => array (84, 172, 104),
        'algae green' => array (33, 195, 111),
        'almost black' => array (7, 13, 13),
        'amber' => array (254, 179, 8),
        'amethyst' => array (155, 95, 192),
        'apple green' => array (118, 205, 38),
        'apricot' => array (255, 177, 109),
        'aqua' => array (19, 234, 201),
        'aqua blue' => array (2, 216, 233),
        'aqua green' => array (18, 225, 147),
        'aquamarine' => array (4, 216, 178),
        'aqua marine' => array (46, 232, 187),
        'army green' => array (75, 93, 22),
        'asparagus' => array (119, 171, 86),
        'aubergine' => array (61, 7, 52),
        'auburn' => array (154, 48, 1),
        'avocado' => array (144, 177, 52),
        'avocado green' => array (135, 169, 34),
        'azul' => array (29, 93, 236),
        'azure' => array (6, 154, 243),
        'baby blue' => array (162, 207, 254),
        'baby green' => array (140, 255, 158),
        'baby pink' => array (255, 183, 206),
        'baby purple' => array (202, 155, 247),
        'banana' => array (255, 255, 126),
        'banana yellow' => array (250, 254, 75),
        'beige' => array (230, 218, 166),
        'berry' => array (153, 15, 75),
        'bile' => array (181, 195, 6),
        'black' => array (0, 0, 0),
        'bland' => array (175, 168, 139),
        'blood' => array (119, 0, 1),
        'blood orange' => array (254, 75, 3),
        'blood red' => array (152, 0, 2),
        'blue' => array (3, 67, 223),
        'blueberry' => array (70, 65, 150),
        'bluegreen' => array (1, 122, 121),
        'blue green' => array (19, 126, 109),
        'blue grey' => array (96, 124, 142),
        'bluegrey' => array (133, 163, 178),
        'blue purple' => array (87, 41, 206),
        'blue violet' => array (93, 6, 233),
        'blue with a hint of purple' => array (83, 60, 198),
        'bluey green' => array (43, 177, 121),
        'bluey grey' => array (137, 160, 176),
        'bluey purple' => array (98, 65, 199),
        'bluish' => array (41, 118, 187),
        'bluish green' => array (16, 166, 116),
        'bluish grey' => array (116, 139, 151),
        'bluish purple' => array (112, 59, 231),
        'blush' => array (242, 158, 142),
        'blush pink' => array (254, 130, 140),
        'boring green' => array (99, 179, 101),
        'bottle green' => array (4, 74, 5),
        'brick' => array (160, 54, 35),
        'brick orange' => array (193, 74, 9),
        'brick red' => array (143, 20, 2),
        'bright aqua' => array (11, 249, 234),
        'bright blue' => array (1, 101, 252),
        'bright cyan' => array (65, 253, 254),
        'bright green' => array (1, 255, 7),
        'bright lavender' => array (199, 96, 255),
        'bright light blue' => array (38, 247, 253),
        'bright light green' => array (45, 254, 84),
        'bright lilac' => array (201, 94, 251),
        'bright lime' => array (135, 253, 5),
        'bright lime green' => array (101, 254, 8),
        'bright magenta' => array (255, 8, 232),
        'bright olive' => array (156, 187, 4),
        'bright orange' => array (255, 91, 0),
        'bright pink' => array (254, 1, 177),
        'bright purple' => array (190, 3, 253),
        'bright red' => array (255, 0, 13),
        'bright sea green' => array (5, 255, 166),
        'bright sky blue' => array (2, 204, 254),
        'bright teal' => array (1, 249, 198),
        'bright turquoise' => array (15, 254, 249),
        'bright violet' => array (173, 10, 253),
        'bright yellow' => array (255, 253, 1),
        'bright yellow green' => array (157, 255, 0),
        'british racing green' => array (5, 72, 13),
        'bronze' => array (168, 121, 0),
        'brown' => array (101, 55, 0),
        'brown green' => array (112, 108, 17),
        'brown grey' => array (141, 132, 104),
        'brownish' => array (156, 109, 87),
        'brownish green' => array (106, 110, 9),
        'brownish grey' => array (134, 119, 95),
        'brownish orange' => array (203, 119, 35),
        'brownish pink' => array (194, 126, 121),
        'brownish purple' => array (118, 66, 78),
        'brownish red' => array (158, 54, 35),
        'brownish yellow' => array (201, 176, 3),
        'brown orange' => array (185, 105, 2),
        'brown red' => array (146, 43, 5),
        'brown yellow' => array (178, 151, 5),
        'browny green' => array (111, 108, 10),
        'browny orange' => array (202, 107, 2),
        'bruise' => array (126, 64, 113),
        'bubblegum' => array (255, 108, 181),
        'bubble gum pink' => array (255, 105, 175),
        'bubblegum pink' => array (254, 131, 204),
        'buff' => array (254, 246, 158),
        'burnt orange' => array (192, 78, 1),
        'burnt red' => array (159, 35, 5),
        'burnt siena' => array (183, 82, 3),
        'burnt sienna' => array (176, 78, 15),
        'burnt umber' => array (160, 69, 14),
        'burnt yellow' => array (213, 171, 9),
        'burple' => array (104, 50, 227),
        'butter' => array (255, 255, 129),
        'butterscotch' => array (253, 177, 71),
        'butter yellow' => array (255, 253, 116),
        'cadet blue' => array (78, 116, 150),
        'camel' => array (198, 159, 89),
        'camo' => array (127, 143, 78),
        'camo green' => array (82, 101, 37),
        'camouflage green' => array (75, 97, 19),
        'canary' => array (253, 255, 99),
        'canary yellow' => array (255, 254, 64),
        'candy pink' => array (255, 99, 233),
        'caramel' => array (175, 111, 9),
        'carmine' => array (157, 2, 22),
        'carnation' => array (253, 121, 143),
        'carnation pink' => array (255, 127, 167),
        'celadon' => array (190, 253, 183),
        'celery' => array (193, 253, 149),
        'cement' => array (165, 163, 145),
        'cerise' => array (222, 12, 98),
        'cerulean' => array (4, 133, 209),
        'cerulean blue' => array (5, 110, 238),
        'charcoal' => array (52, 56, 55),
        'charcoal grey' => array (60, 65, 66),
        'chartreuse' => array (193, 248, 10),
        'cherry' => array (207, 2, 52),
        'cherry red' => array (247, 2, 42),
        'chestnut' => array (116, 40, 2),
        'chocolate' => array (61, 28, 2),
        'chocolate brown' => array (65, 25, 0),
        'cinnamon' => array (172, 79, 6),
        'claret' => array (104, 0, 24),
        'clay' => array (182, 106, 80),
        'clay brown' => array (178, 113, 61),
        'clear blue' => array (36, 122, 253),
        'cloudy blue' => array (172, 194, 217),
        'cobalt' => array (30, 72, 143),
        'cobalt blue' => array (3, 10, 167),
        'cocoa' => array (135, 95, 66),
        'coffee' => array (166, 129, 76),
        'cool blue' => array (73, 132, 184),
        'cool green' => array (51, 184, 100),
        'cool grey' => array (149, 163, 166),
        'copper' => array (182, 99, 37),
        'coral' => array (252, 90, 80),
        'coral pink' => array (255, 97, 99),
        'cornflower' => array (106, 121, 247),
        'cornflower blue' => array (81, 112, 215),
        'cranberry' => array (158, 0, 58),
        'cream' => array (255, 255, 194),
        'creme' => array (255, 255, 182),
        'crimson' => array (140, 0, 15),
        'custard' => array (255, 253, 120),
        'cyan' => array (0, 255, 255),
        'dandelion' => array (254, 223, 8),
        'dark' => array (27, 36, 49),
        'dark aqua' => array (5, 105, 107),
        'dark aquamarine' => array (1, 115, 113),
        'dark beige' => array (172, 147, 98),
        'dark blue' => array (0, 3, 91),
        'darkblue' => array (3, 7, 100),
        'dark blue green' => array (0, 82, 73),
        'dark blue grey' => array (31, 59, 77),
        'dark brown' => array (52, 28, 2),
        'dark coral' => array (207, 82, 78),
        'dark cream' => array (255, 243, 154),
        'dark cyan' => array (10, 136, 138),
        'dark forest green' => array (0, 45, 4),
        'dark fuchsia' => array (157, 7, 89),
        'dark gold' => array (181, 148, 16),
        'dark grass green' => array (56, 128, 4),
        'darkgreen' => array (5, 73, 7),
        'dark green' => array (3, 53, 0),
        'dark green blue' => array (31, 99, 87),
        'dark grey' => array (54, 55, 55),
        'dark grey blue' => array (41, 70, 91),
        'dark hot pink' => array (217, 1, 102),
        'dark indigo' => array (31, 9, 84),
        'darkish blue' => array (1, 65, 130),
        'darkish green' => array (40, 124, 55),
        'darkish pink' => array (218, 70, 125),
        'darkish purple' => array (117, 25, 115),
        'darkish red' => array (169, 3, 8),
        'dark khaki' => array (155, 143, 85),
        'dark lavender' => array (133, 103, 152),
        'dark lilac' => array (156, 109, 165),
        'dark lime' => array (132, 183, 1),
        'dark lime green' => array (126, 189, 1),
        'dark magenta' => array (150, 0, 86),
        'dark maroon' => array (60, 0, 8),
        'dark mauve' => array (135, 76, 98),
        'dark mint' => array (72, 192, 114),
        'dark mint green' => array (32, 192, 115),
        'dark mustard' => array (168, 137, 5),
        'dark navy' => array (0, 4, 53),
        'dark navy blue' => array (0, 2, 46),
        'dark olive' => array (55, 62, 2),
        'dark olive green' => array (60, 77, 3),
        'dark orange' => array (198, 81, 2),
        'dark pastel green' => array (86, 174, 87),
        'dark peach' => array (222, 126, 93),
        'dark periwinkle' => array (102, 95, 209),
        'dark pink' => array (203, 65, 107),
        'dark plum' => array (63, 1, 44),
        'dark purple' => array (53, 6, 62),
        'dark red' => array (132, 0, 0),
        'dark rose' => array (181, 72, 93),
        'dark royal blue' => array (2, 6, 111),
        'dark sage' => array (89, 133, 86),
        'dark salmon' => array (200, 90, 83),
        'dark sand' => array (168, 143, 89),
        'dark seafoam' => array (31, 181, 122),
        'dark seafoam green' => array (62, 175, 118),
        'dark sea green' => array (17, 135, 93),
        'dark sky blue' => array (68, 142, 228),
        'dark slate blue' => array (33, 71, 97),
        'dark tan' => array (175, 136, 74),
        'dark taupe' => array (127, 104, 78),
        'dark teal' => array (1, 77, 78),
        'dark turquoise' => array (4, 92, 90),
        'dark violet' => array (52, 1, 63),
        'dark yellow' => array (213, 182, 10),
        'dark yellow green' => array (114, 143, 2),
        'deep aqua' => array (8, 120, 127),
        'deep blue' => array (4, 2, 115),
        'deep brown' => array (65, 2, 0),
        'deep green' => array (2, 89, 15),
        'deep lavender' => array (141, 94, 183),
        'deep lilac' => array (150, 110, 189),
        'deep magenta' => array (160, 2, 92),
        'deep orange' => array (220, 77, 1),
        'deep pink' => array (203, 1, 98),
        'deep purple' => array (54, 1, 63),
        'deep red' => array (154, 2, 0),
        'deep rose' => array (199, 71, 103),
        'deep sea blue' => array (1, 84, 130),
        'deep sky blue' => array (13, 117, 248),
        'deep teal' => array (0, 85, 90),
        'deep turquoise' => array (1, 115, 116),
        'deep violet' => array (73, 6, 72),
        'denim' => array (59, 99, 140),
        'denim blue' => array (59, 91, 146),
        'desert' => array (204, 173, 96),
        'dirt' => array (138, 110, 69),
        'dirt brown' => array (131, 101, 57),
        'dirty blue' => array (63, 130, 157),
        'dirty green' => array (102, 126, 44),
        'dirty orange' => array (200, 118, 6),
        'dirty pink' => array (202, 123, 128),
        'dirty purple' => array (115, 74, 101),
        'dirty yellow' => array (205, 197, 10),
        'drab' => array (130, 131, 68),
        'drab green' => array (116, 149, 81),
        'dried blood' => array (75, 1, 1),
        'duck egg blue' => array (195, 251, 244),
        'dull blue' => array (73, 117, 156),
        'dull brown' => array (135, 110, 75),
        'dull green' => array (116, 166, 98),
        'dull orange' => array (216, 134, 59),
        'dull pink' => array (213, 134, 157),
        'dull purple' => array (132, 89, 126),
        'dull red' => array (187, 63, 63),
        'dull teal' => array (95, 158, 143),
        'dull yellow' => array (238, 220, 91),
        'dusk' => array (78, 84, 129),
        'dusk blue' => array (38, 83, 141),
        'dusky blue' => array (71, 95, 148),
        'dusky pink' => array (204, 122, 139),
        'dusky purple' => array (137, 91, 123),
        'dusky rose' => array (186, 104, 115),
        'dust' => array (178, 153, 110),
        'dusty blue' => array (90, 134, 173),
        'dusty green' => array (118, 169, 115),
        'dusty lavender' => array (172, 134, 168),
        'dusty orange' => array (240, 131, 58),
        'dusty pink' => array (213, 138, 148),
        'dusty purple' => array (130, 95, 135),
        'dusty red' => array (185, 72, 78),
        'dusty rose' => array (192, 115, 122),
        'dusty teal' => array (76, 144, 133),
        'earth' => array (162, 101, 62),
        'easter green' => array (140, 253, 126),
        'easter purple' => array (192, 113, 254),
        'ecru' => array (254, 255, 202),
        'eggplant' => array (56, 8, 53),
        'eggplant purple' => array (67, 5, 65),
        'egg shell' => array (255, 252, 196),
        'eggshell' => array (255, 255, 212),
        'eggshell blue' => array (196, 255, 247),
        'electric blue' => array (6, 82, 255),
        'electric green' => array (33, 252, 13),
        'electric lime' => array (168, 255, 4),
        'electric pink' => array (255, 4, 144),
        'electric purple' => array (170, 35, 255),
        'emerald' => array (1, 160, 73),
        'emerald green' => array (2, 143, 30),
        'evergreen' => array (5, 71, 42),
        'faded blue' => array (101, 140, 187),
        'faded green' => array (123, 178, 116),
        'faded orange' => array (240, 148, 77),
        'faded pink' => array (222, 157, 172),
        'faded purple' => array (145, 110, 153),
        'faded red' => array (211, 73, 78),
        'faded yellow' => array (254, 255, 127),
        'fawn' => array (207, 175, 123),
        'fern' => array (99, 169, 80),
        'fern green' => array (84, 141, 68),
        'fire engine red' => array (254, 0, 2),
        'flat blue' => array (60, 115, 168),
        'flat green' => array (105, 157, 76),
        'fluorescent green' => array (8, 255, 8),
        'fluro green' => array (10, 255, 2),
        'foam green' => array (144, 253, 169),
        'forest' => array (11, 85, 9),
        'forest green' => array (6, 71, 12),
        'forrest green' => array (21, 68, 6),
        'french blue' => array (67, 107, 173),
        'fresh green' => array (105, 216, 79),
        'frog green' => array (88, 188, 8),
        'fuchsia' => array (237, 13, 217),
        'gold' => array (219, 180, 12),
        'golden' => array (245, 191, 3),
        'golden brown' => array (178, 122, 1),
        'golden rod' => array (249, 188, 8),
        'goldenrod' => array (250, 194, 5),
        'golden yellow' => array (254, 198, 21),
        'grape' => array (108, 52, 97),
        'grapefruit' => array (253, 89, 86),
        'grape purple' => array (93, 20, 81),
        'grass' => array (92, 172, 45),
        'grass green' => array (63, 155, 11),
        'grassy green' => array (65, 156, 3),
        'green' => array (21, 176, 26),
        'green-yellow' => array (221, 214, 24),
        'green apple' => array (94, 220, 31),
        'greenblue' => array (35, 196, 139),
        'green blue' => array (6, 180, 139),
        'green brown' => array (84, 78, 3),
        'green grey' => array (119, 146, 111),
        'greenish' => array (64, 163, 104),
        'greenish beige' => array (201, 209, 121),
        'greenish blue' => array (11, 139, 135),
        'greenish brown' => array (105, 97, 18),
        'greenish cyan' => array (42, 254, 183),
        'greenish grey' => array (150, 174, 141),
        'greenish tan' => array (188, 203, 122),
        'greenish teal' => array (50, 191, 132),
        'greenish turquoise' => array (0, 251, 176),
        'greenish yellow' => array (205, 253, 2),
        'green teal' => array (12, 181, 119),
        'greeny blue' => array (66, 179, 149),
        'greeny brown' => array (105, 96, 6),
        'green yellow' => array (201, 255, 39),
        'greeny grey' => array (126, 160, 122),
        'greeny yellow' => array (198, 248, 8),
        'grey' => array (146, 149, 145),
        'grey blue' => array (107, 139, 164),
        'greyblue' => array (119, 161, 181),
        'grey brown' => array (127, 112, 83),
        'grey green' => array (120, 155, 115),
        'greyish' => array (168, 164, 149),
        'greyish blue' => array (94, 129, 157),
        'greyish brown' => array (122, 106, 79),
        'greyish green' => array (130, 166, 125),
        'greyish pink' => array (200, 141, 148),
        'greyish purple' => array (136, 113, 145),
        'greyish teal' => array (113, 159, 145),
        'grey pink' => array (195, 144, 155),
        'grey purple' => array (130, 109, 140),
        'grey teal' => array (94, 155, 138),
        'gross green' => array (160, 191, 22),
        'gunmetal' => array (83, 98, 103),
        'hazel' => array (142, 118, 24),
        'highlighter green' => array (27, 252, 6),
        'hospital green' => array (155, 229, 170),
        'hot green' => array (37, 255, 41),
        'hot magenta' => array (245, 4, 201),
        'hot pink' => array (255, 2, 141),
        'hot purple' => array (203, 0, 245),
        'hunter green' => array (11, 64, 8),
        'ice' => array (214, 255, 250),
        'ice blue' => array (215, 255, 254),
        'icky green' => array (143, 174, 34),
        'indian red' => array (133, 14, 4),
        'indigo' => array (56, 2, 130),
        'indigo blue' => array (58, 24, 177),
        'iris' => array (98, 88, 196),
        'irish green' => array (1, 149, 41),
        'ivory' => array (255, 255, 203),
        'jade' => array (31, 167, 116),
        'jade green' => array (43, 175, 106),
        'jungle green' => array (4, 130, 67),
        'key lime' => array (174, 255, 110),
        'khaki' => array (170, 166, 98),
        'khaki green' => array (114, 134, 57),
        'kiwi' => array (156, 239, 67),
        'kiwi green' => array (142, 229, 63),
        'lavender' => array (199, 159, 239),
        'lavender blue' => array (139, 136, 248),
        'lavender pink' => array (221, 133, 215),
        'lawn green' => array (77, 164, 9),
        'leaf' => array (113, 170, 52),
        'leaf green' => array (92, 169, 4),
        'leafy green' => array (81, 183, 59),
        'leather' => array (172, 116, 52),
        'lemon' => array (253, 255, 82),
        'lemon green' => array (173, 248, 2),
        'lemon lime' => array (191, 254, 40),
        'lemon yellow' => array (253, 255, 56),
        'lichen' => array (143, 182, 123),
        'light aqua' => array (140, 255, 219),
        'light aquamarine' => array (123, 253, 199),
        'light beige' => array (255, 254, 182),
        'light blue' => array (149, 208, 252),
        'lightblue' => array (123, 200, 246),
        'light blue green' => array (126, 251, 179),
        'light blue grey' => array (183, 201, 226),
        'light bluish green' => array (118, 253, 168),
        'light bright green' => array (83, 254, 92),
        'light brown' => array (173, 129, 80),
        'light burgundy' => array (168, 65, 91),
        'light cyan' => array (172, 255, 252),
        'light eggplant' => array (137, 69, 133),
        'lighter green' => array (117, 253, 99),
        'lighter purple' => array (165, 90, 244),
        'light forest green' => array (79, 145, 83),
        'light gold' => array (253, 220, 92),
        'light grass green' => array (154, 247, 100),
        'lightgreen' => array (118, 255, 123),
        'light green' => array (150, 249, 123),
        'light green blue' => array (86, 252, 162),
        'light greenish blue' => array (99, 247, 180),
        'light grey' => array (216, 220, 214),
        'light grey blue' => array (157, 188, 212),
        'light grey green' => array (183, 225, 161),
        'light indigo' => array (109, 90, 207),
        'lightish blue' => array (61, 122, 253),
        'lightish green' => array (97, 225, 96),
        'lightish purple' => array (165, 82, 230),
        'lightish red' => array (254, 47, 74),
        'light khaki' => array (230, 242, 162),
        'light lavendar' => array (239, 192, 254),
        'light lavender' => array (223, 197, 254),
        'light light blue' => array (202, 255, 251),
        'light light green' => array (200, 255, 176),
        'light lilac' => array (237, 200, 255),
        'light lime' => array (174, 253, 108),
        'light lime green' => array (185, 255, 102),
        'light magenta' => array (250, 95, 247),
        'light maroon' => array (162, 72, 87),
        'light mauve' => array (194, 146, 161),
        'light mint' => array (182, 255, 187),
        'light mint green' => array (166, 251, 178),
        'light moss green' => array (166, 200, 117),
        'light mustard' => array (247, 213, 96),
        'light navy' => array (21, 80, 132),
        'light navy blue' => array (46, 90, 136),
        'light neon green' => array (78, 253, 84),
        'light olive' => array (172, 191, 105),
        'light olive green' => array (164, 190, 92),
        'light orange' => array (253, 170, 72),
        'light pastel green' => array (178, 251, 165),
        'light peach' => array (255, 216, 177),
        'light pea green' => array (196, 254, 130),
        'light periwinkle' => array (193, 198, 252),
        'light pink' => array (255, 209, 223),
        'light plum' => array (157, 87, 131),
        'light purple' => array (191, 119, 246),
        'light red' => array (255, 71, 76),
        'light rose' => array (255, 197, 203),
        'light royal blue' => array (58, 46, 254),
        'light sage' => array (188, 236, 172),
        'light salmon' => array (254, 169, 147),
        'light seafoam' => array (160, 254, 191),
        'light seafoam green' => array (167, 255, 181),
        'light sea green' => array (152, 246, 176),
        'light sky blue' => array (198, 252, 255),
        'light tan' => array (251, 238, 172),
        'light teal' => array (144, 228, 193),
        'light turquoise' => array (126, 244, 204),
        'light urple' => array (179, 111, 246),
        'light violet' => array (214, 180, 252),
        'light yellow' => array (255, 254, 122),
        'light yellow green' => array (204, 253, 127),
        'light yellowish green' => array (194, 255, 137),
        'lilac' => array (206, 162, 253),
        'liliac' => array (196, 142, 253),
        'lime' => array (170, 255, 50),
        'lime green' => array (137, 254, 5),
        'lime yellow' => array (208, 254, 29),
        'lipstick' => array (213, 23, 78),
        'lipstick red' => array (192, 2, 47),
        'macaroni and cheese' => array (239, 180, 53),
        'magenta' => array (194, 0, 120),
        'mahogany' => array (74, 1, 0),
        'maize' => array (244, 208, 84),
        'mango' => array (255, 166, 43),
        'manilla' => array (255, 250, 134),
        'marigold' => array (252, 192, 6),
        'marine' => array (4, 46, 96),
        'marine blue' => array (1, 56, 106),
        'maroon' => array (101, 0, 33),
        'mauve' => array (174, 113, 129),
        'medium blue' => array (44, 111, 187),
        'medium brown' => array (127, 81, 18),
        'medium green' => array (57, 173, 72),
        'medium grey' => array (125, 127, 124),
        'medium pink' => array (243, 97, 150),
        'medium purple' => array (158, 67, 162),
        'melon' => array (255, 120, 85),
        'metallic blue' => array (79, 115, 142),
        'mid blue' => array (39, 106, 179),
        'mid green' => array (80, 167, 71),
        'midnight' => array (3, 1, 45),
        'midnight blue' => array (2, 0, 53),
        'midnight purple' => array (40, 1, 55),
        'military green' => array (102, 124, 62),
        'milk chocolate' => array (127, 78, 30),
        'mint' => array (159, 254, 176),
        'mint green' => array (143, 255, 159),
        'minty green' => array (11, 247, 125),
        'mocha' => array (157, 118, 81),
        'moss' => array (118, 153, 88),
        'moss green' => array (101, 139, 56),
        'mossy green' => array (99, 139, 39),
        'mud' => array (115, 92, 18),
        'mud brown' => array (96, 70, 15),
        'muddy brown' => array (136, 104, 6),
        'muddy green' => array (101, 116, 50),
        'muddy yellow' => array (191, 172, 5),
        'mud green' => array (96, 102, 2),
        'mulberry' => array (146, 10, 78),
        'murky green' => array (108, 122, 14),
        'mushroom' => array (186, 158, 136),
        'mustard' => array (206, 179, 1),
        'mustard brown' => array (172, 126, 4),
        'mustard green' => array (168, 181, 4),
        'mustard yellow' => array (210, 189, 10),
        'muted blue' => array (59, 113, 159),
        'muted green' => array (95, 160, 82),
        'muted pink' => array (209, 118, 143),
        'muted purple' => array (128, 91, 135),
        'navy' => array (1, 21, 62),
        'navy blue' => array (0, 17, 70),
        'navy green' => array (53, 83, 10),
        'neon blue' => array (4, 217, 255),
        'neon green' => array (12, 255, 12),
        'neon pink' => array (254, 1, 154),
        'neon purple' => array (188, 19, 254),
        'neon red' => array (255, 7, 58),
        'neon yellow' => array (207, 255, 4),
        'nice blue' => array (16, 122, 176),
        'night blue' => array (4, 3, 72),
        'ocean' => array (1, 123, 146),
        'ocean blue' => array (3, 113, 156),
        'ocean green' => array (61, 153, 115),
        'ocher' => array (191, 155, 12),
        'ochre' => array (191, 144, 5),
        'off blue' => array (86, 132, 174),
        'off green' => array (107, 163, 83),
        'off white' => array (255, 255, 228),
        'off yellow' => array (241, 243, 63),
        'old pink' => array (199, 121, 134),
        'old rose' => array (200, 127, 137),
        'olive' => array (110, 117, 14),
        'olive brown' => array (100, 84, 3),
        'olive drab' => array (111, 118, 50),
        'olive green' => array (103, 122, 4),
        'olive yellow' => array (194, 183, 9),
        'orange' => array (249, 115, 6),
        'orange brown' => array (190, 100, 0),
        'orangeish' => array (253, 141, 73),
        'orange pink' => array (255, 111, 82),
        'orange red' => array (253, 65, 30),
        'orangered' => array (254, 66, 15),
        'orangey brown' => array (177, 96, 2),
        'orange yellow' => array (255, 173, 1),
        'orangey red' => array (250, 66, 36),
        'orangey yellow' => array (253, 185, 21),
        'orangish' => array (252, 130, 74),
        'orangish brown' => array (178, 95, 3),
        'orangish red' => array (244, 54, 5),
        'orchid' => array (200, 117, 196),
        'pale' => array (255, 249, 208),
        'pale aqua' => array (184, 255, 235),
        'pale blue' => array (208, 254, 254),
        'pale brown' => array (177, 145, 110),
        'pale cyan' => array (183, 255, 250),
        'pale gold' => array (253, 222, 108),
        'pale green' => array (199, 253, 181),
        'pale grey' => array (253, 253, 254),
        'pale lavender' => array (238, 207, 254),
        'pale light green' => array (177, 252, 153),
        'pale lilac' => array (228, 203, 255),
        'pale lime' => array (190, 253, 115),
        'pale lime green' => array (177, 255, 101),
        'pale magenta' => array (215, 103, 173),
        'pale mauve' => array (254, 208, 252),
        'pale olive' => array (185, 204, 129),
        'pale olive green' => array (177, 210, 123),
        'pale orange' => array (255, 167, 86),
        'pale peach' => array (255, 229, 173),
        'pale pink' => array (255, 207, 220),
        'pale purple' => array (183, 144, 212),
        'pale red' => array (217, 84, 77),
        'pale rose' => array (253, 193, 197),
        'pale salmon' => array (255, 177, 154),
        'pale sky blue' => array (189, 246, 254),
        'pale teal' => array (130, 203, 178),
        'pale turquoise' => array (165, 251, 213),
        'pale violet' => array (206, 174, 250),
        'pale yellow' => array (255, 255, 132),
        'parchment' => array (254, 252, 175),
        'pastel blue' => array (162, 191, 254),
        'pastel green' => array (176, 255, 157),
        'pastel orange' => array (255, 150, 79),
        'pastel pink' => array (255, 186, 205),
        'pastel purple' => array (202, 160, 255),
        'pastel red' => array (219, 88, 86),
        'pastel yellow' => array (255, 254, 113),
        'pea' => array (164, 191, 32),
        'peach' => array (255, 176, 124),
        'peachy pink' => array (255, 154, 138),
        'peacock blue' => array (1, 103, 149),
        'pea green' => array (142, 171, 18),
        'pear' => array (203, 248, 95),
        'pea soup' => array (146, 153, 1),
        'pea soup green' => array (148, 166, 23),
        'periwinkle' => array (142, 130, 254),
        'periwinkle blue' => array (143, 153, 251),
        'perrywinkle' => array (143, 140, 231),
        'petrol' => array (0, 95, 106),
        'pig pink' => array (231, 142, 165),
        'pine' => array (43, 93, 52),
        'pine green' => array (10, 72, 30),
        'pink' => array (255, 129, 192),
        'pinkish' => array (212, 106, 126),
        'pinkish brown' => array (177, 114, 97),
        'pinkish grey' => array (200, 172, 169),
        'pinkish orange' => array (255, 114, 76),
        'pinkish purple' => array (214, 72, 215),
        'pinkish red' => array (241, 12, 69),
        'pinkish tan' => array (217, 155, 130),
        'pink purple' => array (219, 75, 218),
        'pink red' => array (245, 5, 79),
        'pinky' => array (252, 134, 170),
        'pinky purple' => array (201, 76, 190),
        'pinky red' => array (252, 38, 71),
        'pistachio' => array (192, 250, 139),
        'plum' => array (88, 15, 65),
        'plum purple' => array (78, 5, 80),
        'poison green' => array (64, 253, 20),
        'poo brown' => array (136, 95, 1),
        'powder blue' => array (177, 209, 252),
        'powder pink' => array (255, 178, 208),
        'primary blue' => array (8, 4, 249),
        'prussian blue' => array (0, 69, 119),
        'puce' => array (165, 126, 82),
        'pumpkin' => array (225, 119, 1),
        'pumpkin orange' => array (251, 125, 7),
        'pure blue' => array (2, 3, 226),
        'purple' => array (126, 30, 156),
        'purple blue' => array (99, 45, 233),
        'purple brown' => array (103, 58, 63),
        'purple grey' => array (134, 111, 133),
        'purpleish' => array (152, 86, 141),
        'purpleish blue' => array (97, 64, 239),
        'purpleish pink' => array (223, 78, 200),
        'purple pink' => array (224, 63, 216),
        'purple red' => array (153, 1, 71),
        'purpley' => array (135, 86, 228),
        'purpley blue' => array (95, 52, 231),
        'purpley grey' => array (148, 126, 148),
        'purpley pink' => array (200, 60, 185),
        'purplish' => array (148, 86, 140),
        'purplish blue' => array (96, 30, 249),
        'purplish brown' => array (107, 66, 71),
        'purplish grey' => array (122, 104, 127),
        'purplish pink' => array (206, 93, 174),
        'purplish red' => array (176, 5, 75),
        'purply' => array (152, 63, 178),
        'purply blue' => array (102, 26, 238),
        'purply pink' => array (240, 117, 230),
        'putty' => array (190, 174, 138),
        'racing green' => array (1, 70, 0),
        'radioactive green' => array (44, 250, 31),
        'raspberry' => array (176, 1, 73),
        'raw sienna' => array (154, 98, 0),
        'raw umber' => array (167, 94, 9),
        'really light blue' => array (212, 255, 255),
        'red' => array (229, 0, 0),
        'red brown' => array (139, 46, 22),
        'reddish' => array (196, 66, 64),
        'reddish brown' => array (127, 43, 10),
        'reddish grey' => array (153, 117, 112),
        'reddish orange' => array (248, 72, 28),
        'reddish pink' => array (254, 44, 84),
        'reddish purple' => array (145, 9, 81),
        'reddy brown' => array (110, 16, 5),
        'red orange' => array (253, 60, 6),
        'red pink' => array (250, 42, 85),
        'red purple' => array (130, 7, 71),
        'red violet' => array (158, 1, 104),
        'red wine' => array (140, 0, 52),
        'rich blue' => array (2, 27, 249),
        'rich purple' => array (114, 0, 88),
        'robin egg' => array (109, 237, 253),
        'robin egg blue' => array (152, 239, 249),
        'rosa' => array (254, 134, 164),
        'rose' => array (207, 98, 117),
        'rose pink' => array (247, 135, 154),
        'rose red' => array (190, 1, 60),
        'rosy pink' => array (246, 104, 142),
        'rouge' => array (171, 18, 57),
        'royal' => array (12, 23, 147),
        'royal blue' => array (5, 4, 170),
        'royal purple' => array (75, 0, 110),
        'ruby' => array (202, 1, 71),
        'russet' => array (161, 57, 5),
        'rust' => array (168, 60, 9),
        'rust brown' => array (139, 49, 3),
        'rust orange' => array (196, 85, 8),
        'rust red' => array (170, 39, 4),
        'rusty orange' => array (205, 89, 9),
        'rusty red' => array (175, 47, 13),
        'saffron' => array (254, 178, 9),
        'sage' => array (135, 174, 115),
        'sage green' => array (136, 179, 120),
        'salmon' => array (255, 121, 108),
        'salmon pink' => array (254, 123, 124),
        'sand' => array (226, 202, 118),
        'sand brown' => array (203, 165, 96),
        'sandstone' => array (201, 174, 116),
        'sandy' => array (241, 218, 122),
        'sandy brown' => array (196, 166, 97),
        'sand yellow' => array (252, 225, 102),
        'sandy yellow' => array (253, 238, 115),
        'sap green' => array (92, 139, 21),
        'sapphire' => array (33, 56, 171),
        'scarlet' => array (190, 1, 25),
        'sea' => array (60, 153, 146),
        'sea blue' => array (4, 116, 149),
        'seafoam' => array (128, 249, 173),
        'seafoam blue' => array (120, 209, 182),
        'seafoam green' => array (122, 249, 171),
        'sea green' => array (83, 252, 161),
        'seaweed' => array (24, 209, 123),
        'seaweed green' => array (53, 173, 107),
        'sepia' => array (152, 94, 43),
        'shamrock' => array (1, 180, 76),
        'shamrock green' => array (2, 193, 77),
        'shocking pink' => array (254, 2, 162),
        'sick green' => array (157, 185, 44),
        'sickly green' => array (148, 178, 28),
        'sickly yellow' => array (208, 228, 41),
        'sienna' => array (169, 86, 30),
        'silver' => array (197, 201, 199),
        'sky' => array (130, 202, 252),
        'sky blue' => array (117, 187, 253),
        'slate' => array (81, 101, 114),
        'slate blue' => array (91, 124, 153),
        'slate green' => array (101, 141, 109),
        'slate grey' => array (89, 101, 109),
        'slime green' => array (153, 204, 4),
        'soft blue' => array (100, 136, 234),
        'soft green' => array (111, 194, 118),
        'soft pink' => array (253, 176, 192),
        'soft purple' => array (166, 111, 181),
        'spearmint' => array (30, 248, 118),
        'spring green' => array (169, 249, 113),
        'spruce' => array (10, 95, 56),
        'squash' => array (242, 171, 21),
        'steel' => array (115, 133, 149),
        'steel blue' => array (90, 125, 154),
        'steel grey' => array (111, 130, 138),
        'stone' => array (173, 165, 135),
        'stormy blue' => array (80, 123, 156),
        'straw' => array (252, 246, 121),
        'strawberry' => array (251, 41, 67),
        'strong blue' => array (12, 6, 247),
        'strong pink' => array (255, 7, 137),
        'sunflower' => array (255, 197, 18),
        'sunflower yellow' => array (255, 218, 3),
        'sunny yellow' => array (255, 249, 23),
        'sunshine yellow' => array (255, 253, 55),
        'sun yellow' => array (255, 223, 34),
        'swamp' => array (105, 131, 57),
        'swamp green' => array (116, 133, 0),
        'tan' => array (209, 178, 111),
        'tan brown' => array (171, 126, 76),
        'tangerine' => array (255, 148, 8),
        'tan green' => array (169, 190, 112),
        'taupe' => array (185, 162, 129),
        'tea' => array (101, 171, 124),
        'tea green' => array (189, 248, 163),
        'teal' => array (2, 147, 134),
        'teal blue' => array (1, 136, 159),
        'teal green' => array (37, 163, 111),
        'tealish' => array (36, 188, 168),
        'tealish green' => array (12, 220, 115),
        'terracota' => array (203, 104, 67),
        'terra cotta' => array (201, 100, 59),
        'terracotta' => array (202, 102, 65),
        'tomato' => array (239, 64, 38),
        'tomato red' => array (236, 45, 1),
        'topaz' => array (19, 187, 175),
        'toupe' => array (199, 172, 125),
        'toxic green' => array (97, 222, 42),
        'tree green' => array (42, 126, 25),
        'true blue' => array (1, 15, 204),
        'true green' => array (8, 148, 4),
        'turquoise' => array (6, 194, 172),
        'turquoise blue' => array (6, 177, 196),
        'turquoise green' => array (4, 244, 137),
        'turtle green' => array (117, 184, 79),
        'twilight' => array (78, 81, 139),
        'twilight blue' => array (10, 67, 122),
        'ultramarine' => array (32, 0, 177),
        'ultramarine blue' => array (24, 5, 219),
        'umber' => array (178, 100, 0),
        'velvet' => array (117, 8, 81),
        'vermillion' => array (244, 50, 12),
        'very dark blue' => array (0, 1, 51),
        'very dark brown' => array (29, 2, 0),
        'very dark green' => array (6, 46, 3),
        'very dark purple' => array (42, 1, 52),
        'very light blue' => array (213, 255, 255),
        'very light brown' => array (211, 182, 131),
        'very light green' => array (209, 255, 189),
        'very light pink' => array (255, 244, 242),
        'very light purple' => array (246, 206, 252),
        'very pale blue' => array (214, 255, 254),
        'very pale green' => array (207, 253, 188),
        'vibrant blue' => array (3, 57, 248),
        'vibrant green' => array (10, 221, 8),
        'vibrant purple' => array (173, 3, 222),
        'violet' => array (154, 14, 234),
        'violet blue' => array (81, 10, 201),
        'violet pink' => array (251, 95, 252),
        'violet red' => array (165, 0, 85),
        'viridian' => array (30, 145, 103),
        'vivid blue' => array (21, 46, 255),
        'vivid green' => array (47, 239, 16),
        'vivid purple' => array (153, 0, 250),
        'warm blue' => array (75, 87, 219),
        'warm brown' => array (150, 78, 2),
        'warm grey' => array (151, 138, 132),
        'warm pink' => array (251, 85, 129),
        'warm purple' => array (149, 46, 143),
        'washed out green' => array (188, 245, 166),
        'water blue' => array (14, 135, 204),
        'watermelon' => array (253, 70, 89),
        'weird green' => array (58, 229, 127),
        'wheat' => array (251, 221, 126),
        'white' => array (255, 255, 255),
        'windows blue' => array (55, 120, 191),
        'wine' => array (128, 1, 63),
        'wine red' => array (123, 3, 35),
        'wintergreen' => array (32, 249, 134),
        'wisteria' => array (168, 125, 194),
        'yellow' => array (255, 255, 20),
        'yellow brown' => array (183, 148, 0),
        'yellowgreen' => array (187, 249, 15),
        'yellow green' => array (192, 251, 45),
        'yellowish' => array (250, 238, 102),
        'yellowish brown' => array (155, 122, 1),
        'yellowish green' => array (176, 221, 22),
        'yellowish orange' => array (255, 171, 15),
        'yellowish tan' => array (252, 252, 129),
        'yellow ochre' => array (203, 157, 6),
        'yellow orange' => array (252, 176, 1),
        'yellow tan' => array (255, 227, 110),
        'yellowy brown' => array (174, 139, 12),
        'yellowy green' => array (191, 241, 40),
    );
    
    /*
     * Avaliable conversions and quality array
     *
     * @uses Used by load(), save(), convert()
     * @var array
     */
    public $availableConv = array(
        'gif' => FALSE, // #!important; no quality control for gifs
        'png' => 9, // Maximum PNG quality
        'jpeg' => 100, // maximum jpeg quality
    );

    /*
     * GDF default font
     * Font List:
     * 1-> width=5 px, height=8 px
     * 2-> width=6 px, height=13 px
     * 3-> width=7 px, height=13 px
     * 4-> width=8 px, height=16 px
     * 5-> width=9 px, height=15 px
     *
     * @uses Used by text()
     * @var int
     */
    public $defaultFont = 4;

    /*
     * Default text color (hexa)
     *
     * @uses Used by text()
     * @var string
     */
    public $defaulttextColor = '#000000';

    /*
     * Default text Background - color (hexa) and transparency (0,opaque-127,transparent)
     *
     * @uses Used by filter(), text()
     * @var array
     */
    public $defaulttextBg = array('#ffffff', 127);

    // Cache
    protected static $_cache = array();
    
    /**
     * Transform a resource into a base64 string
     * 
     * @param string $resource Resource
     * @param string $type     Type (default "png"). Allowed values: <ul>
     * <li>png</li>
     * <li>jpg</li>
     * <li>jpeg</li>
     * <li>gif</li>
     * </ul>
     * @param int    $quality  Quality
     * @return string Base 64 - formatted image
     * @throws Exception
     */
    public function resourceToBase64($resource, $type = 'png', $quality = 100) {
        // Not a valid resource
        if (!is_resource($resource)) {
            throw new Exception('Not a valid resource');
        }
        
        // Get the function name
        switch ($type) {
            case 'jpg':
            case 'jpeg':
                $type = 'jpeg';
                $function = 'imagejpeg';
                break;
            
            case 'gif':
                $function = 'imagegif';
            
            case 'png':
            default:
                $function = 'imagepng';
                break;
        }
        
        // Set the quality
        if (!is_int($this->availableConv[$type])) {
            $quality = null;
        } elseif ($quality > $this->availableConv[$type]) {
            $quality = $this->availableConv[$type];
        } elseif ($quality < 0) {
            $quality = 0;
        }
        
        // Prepare the Output buffer
        ob_start();
        
        // Output the image
        $function($resource, null, $quality);

        // Get the binary contents
        $binary = ob_get_clean();
        
        // Return the base 64 format
        return base64_encode($binary);
    }
    
    /**
     * Save a text in a PNG image with the current date
     * 
     * @param string $stringBase64 Base64 encrpted text to save
     * @param string $path         PNG path
     * @return null
     */
    public function encrypt($stringBase64, $path) {
        // Get the min width
        $actualMinWidth = ceil(pow(strlen($stringBase64) / 3 + 1, 0.5));
        
        // Internal minimum width
        $width = $actualMinWidth <= 50 ? 50 : $actualMinWidth;
        
        // Prepare a canvas
        $canvas = $this->canvas($width, $width);
        
        // Put the date down the middle
        $text = $this->text(date('d/m/Y'), '#000000', floor($width / 5));
        
        // Store the date
        $canvas = $this->overlay($text, $canvas);
        
        // Prepare the color ids
        $colorIds = array();
        
        // Set the alpha blending to false
        imagealphablending($canvas, false);
        
        // Increment
        $increment = 0;
        
        // Go through each pixel
        for ($j = 0; $j < $width; $j++) {
            for ($i = 0; $i < $width; $i++) {
                // Rows passed
                $sum = $increment * 3;
                
                // Letters
                $redLetter   = isset($stringBase64[$sum])     ? $stringBase64[$sum]     : null;
                $greenLetter = isset($stringBase64[$sum + 1]) ? $stringBase64[$sum + 1] : null;
                $blueLetter  = isset($stringBase64[$sum + 2]) ? $stringBase64[$sum + 2] : null;
                
                // Get the actual int values
                $red   = null === $redLetter   ? 0 : ord($redLetter);
                $green = null === $greenLetter ? 0 : ord($greenLetter);
                $blue  = null === $blueLetter  ? 0 : ord($blueLetter);

                // Get the color
                $rgba = imagecolorsforindex($canvas, imagecolorat($canvas, $i, $j));

                // Get the color id name
                $colorIdName = $red . ',' . $green . ',' . $blue . ':' . $rgba['alpha'];

                // Color ID set for the first time
                if (!isset($colorIds[$colorIdName])) {
                    // Create the new color
                    $colorIds[$colorIdName] = imagecolorallocatealpha($canvas, $red, $green, $blue, $rgba['alpha']);
                }

                // Set it in place
                imagesetpixel($canvas, $i, $j, $colorIds[$colorIdName]);
                
                // Need to stop?
                if (null === $redLetter || null === $greenLetter || null === $blueLetter) {
                    break 2;
                }
                
                // Increment
                $increment++;
            }
        }

        // Save it
        $this->save($canvas, 'png', $path);
    }
    
    /**
     * Get a line with the defined pixels
     * 
     * @see Image->readLine
     * @param array[] $colorsRgb Array of RGB arrays, each one counting as a pixel
     * @return resource
     */
    public function writeLine(Array $colorsRgb) {
        // Prepare a canvas
        $canvas = $this->canvas(count($colorsRgb), 1);
        
        // Prepare the color ids
        $colorIds = array();
        
        // Go through the colors
        foreach (array_values($colorsRgb) as $index => $rgb) {
            // Must be a valid list
            if (!is_array($rgb) || count($rgb) < 3) {
                continue;
            }
            
            // Get the values
            list($red, $green, $blue) = array_values($rgb);
            
            // Get the color id name
            $colorIdName = $red . ',' . $green . ',' . $blue;

            // Color ID set for the first time
            if (!isset($colorIds[$colorIdName])) {
                // Create the new color
                $colorIds[$colorIdName] = imagecolorallocate($canvas, $red, $green, $blue);
            }
            
            // Set it in place
            imagesetpixel($canvas, $index, 0, $colorIds[$colorIdName]);
        }
        
        // All done
        return $canvas;
    }
    
    /**
     * Read the defined pixels in a line resource
     * 
     * @see Image->writeLine
     * @param resource $lineResource
     * @return array[] Array of RGB arrays, each one counting as a pixel
     */
    public function readLine($lineResource) {
        // Make it true
        $lineResource = $this->truecolor($lineResource);
        
        // Get the line width
        $lineWidth = imagesx($lineResource);
        
        // Prepare the result
        $result = array();
        
        // Go through each pixel
        for ($i = 0; $i < $lineWidth; $i++) {
            // Get the color
            list($red, $green, $blue) = array_values(imagecolorsforindex($lineResource, imagecolorat($lineResource, $i, 0)));

            // Store the color
            $result[] = array($red, $green, $blue);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Decrypt a message
     * 
     * @param string $path Encrypted PNG path
     * @return string Decoded string
     * @throws Exception
     */
    public function decrypt($path) {
        // Load the image
        $canvas = $this->load($path);
        
        // Prepare the result
        $result = '';
        
        // Go through each pixel
        for ($j = 0; $j < imagesy($canvas); $j++) {
            for ($i = 0; $i < imagesx($canvas); $i++) {               
                // Get the colors
                $rgba = array_values(imagecolorsforindex($canvas, imagecolorat($canvas, $i, $j)));

                // Remove the alpha
                unset($rgba[3]);
                
                // Go through the letters
                foreach ($rgba as $ord) {
                    // Need to stop
                    if (0 === $ord) {
                        break 2;
                    }
                    
                    // Invalid sequence
                    if (!preg_match('%[a-z0-9\=\+\/]%i', chr($ord))) {
                        throw new Exception('Invalid image to decode');
                    }
                    
                    // Get the actual character
                    $result .= chr($ord);
                }
            }
        }
        
        // All done
        return $result;
    }
    
    /**
     * HEX to RGB
     * 
     * @param string $color HEX color (support for ARGB added)
     * @return array R,G,B,A(0-255)
     */
    public function hexToRgb($color) {
        // Replace the starting #
        $color = preg_replace('%^#%', '', $color);
        
        // Short color
        if (strlen($color) < 6) {
            $color = str_pad($color, 6, $color, STR_PAD_RIGHT);
        }
        
        // Invalid lenght
        if (8 !== strlen($color)) {
            $color = str_pad($color, 8, "ff", STR_PAD_LEFT);
        }
        
        // Get the channels
        $channels = array_map('hexdec', str_split($color, 2));
        
        // Set the alpha channel
        $channels[] = array_shift($channels);
        
        // Return the result
        return $channels;
    }

    /**
     * RGB to HEX (#+6 characters)
     * 
     * @param array $rgbArray RGB colors
     */
    public function rgbToHex($rgbArray) {
        // Get the red, green and blue
        list($red, $green, $blue) = array_values($rgbArray);
        
        // Make the conversions
        $red = str_pad(dechex($red), 2, '0', STR_PAD_LEFT);
        $green = str_pad(dechex($green), 2, '0', STR_PAD_LEFT);
        $blue = str_pad(dechex($blue), 2, '0', STR_PAD_LEFT);

        // Return the color
        return '#' . $red . $green . $blue;
    }
    
    /**
     * Get a chached value
     * 
     * @param mixed  $key    Key
     * @param string $prefix Cache prefix
     * @return mixed Null if nothing found
     */
    protected static function _cacheGet($key, $prefix = self::CACHE_PREFIX_RGB_TO_HSL) {
        // Get the key
        $fullKey = self::_cacheKey($key, $prefix);
        
        // Found a cached element
        if (isset(self::$_cache[$fullKey])) {
            // Return it
            return self::$_cache[$fullKey];
        }
        
        // Nothing found
        return null;
    }
    
    /**
     * Set element in cache
     * 
     * @param mixed  $key    Key
     * @param mixed  $value  Value - anything except null
     * @param string $prefix Cache prefix
     * @return boolean False if value is null
     */
    protected static function _cacheSet($key, $value, $prefix = self::CACHE_PREFIX_RGB_TO_HSL) {
        // Test the value
        if (null !== $value) {
            self::$_cache[self::_cacheKey($key, $prefix)] = $value;
            
            // All went well
            return true;
        }
        
        // Invalid value
        return false;
    }
    
    /**
     * Prepare a cache key as a string
     * 
     * @param mixed  $key    Key
     * @param string $prefix Cache prefix
     * @return string Cache key
     */
    protected static function _cacheKey($key, $prefix = self::CACHE_PREFIX_RGB_TO_HSL) {
        // Prepare the array
        if (!is_array($key)) {
            $key = array($key);
        }

        // Prepare the elements
        $elements = array();
        
        // Go through the key
        foreach ($key as $item) {
            // Not a string or a number
            if (!is_string($item) && !is_numeric($item)) {
                // Start the buffer
                ob_start();

                // Get the result
                var_export($item);

                // Store the entry
                $item = base64_encode(ob_get_clean());
            }
            
            // Store as base 64
            $elements[] = $item;
        }

        return $prefix . '_' . implode('_', $elements);
    }
    
    /**
     * Cache key helper
     * 
     * @param string $imagePathOrDetails
     * @return type
     */
    protected static function _cacheKeyMixed($imagePathOrDetails) {
        
        
        // All done
        return $entry;
    }
    
    /**
     * RGB to HSL
     * 
     * @param array $rgbArray RGB colors
     */
    public function rgbToHsl($rgbArray) {
        // Get R, G, B
        list($r, $g, $b) = array_values($rgbArray);

        // Call the cache
        if (null !== $cachedHsl = self::_cacheGet(array($r, $g, $b), self::CACHE_PREFIX_RGB_TO_HSL)) {
            return $cachedHsl;
        }
        
        // Reduce them
        $r /= 255;
        $g /= 255;
        $b /= 255;

        // Get the max
        $max = max($r, $g, $b);
        
        // Get the min
        $min = min($r, $g, $b);
        
        // Get the luminance
        $l = ( $max + $min ) / 2.0;
        
        // Get the difference
        $d = $max - $min;

        // No difference
        if ($d == 0) {
            $h = $s = 0; // achromatic
        } else {
            $s = $d / ( 1 - abs(2 * $l - 1) );
            switch ($max) {
                case $r:
                    $h = 60 * fmod(( ( $g - $b ) / $d), 6);
                    if ($b > $g) {
                        $h += 360;
                    }
                    break;

                case $g:
                    $h = 60 * ( ( $b - $r ) / $d + 2 );
                    break;

                case $b:
                    $h = 60 * ( ( $r - $g ) / $d + 4 );
                    break;
            }
        }

        // Prepare HLS
        $result = array(round($h, 0), $s, $l);
        
        // Save to cache
        self::_cacheSet(array($r, $g, $b), $result, self::CACHE_PREFIX_RGB_TO_HSL);
        
        // All done
        return $result;
    }

    /**
     * Convert HSL to RGB
     * 
     * @param array $hslArray HSL array
     * @return array
     */
    public function hslToRgb($hslArray) {
        // Get H, S, L
        list($h, $s, $l) = array_values($hslArray);
        
        // Call the cache
        if (null !== $cachedRgb = self::_cacheGet(array($h, $s, $l), self::CACHE_PREFIX_HSL_TO_RGB)) {
            return $cachedRgb;
        }
        
        // Get stuff
        $c = ( 1 - abs(2 * $l - 1) ) * $s;
        $x = $c * ( 1 - abs(fmod(( $h / 60), 2) - 1) );
        $m = $l - ( $c / 2 );

        if ($h < 60) {
            $r = $c;
            $g = $x;
            $b = 0;
        } else if ($h < 120) {
            $r = $x;
            $g = $c;
            $b = 0;
        } else if ($h < 180) {
            $r = 0;
            $g = $c;
            $b = $x;
        } else if ($h < 240) {
            $r = 0;
            $g = $x;
            $b = $c;
        } else if ($h < 300) {
            $r = $x;
            $g = 0;
            $b = $c;
        } else {
            $r = $c;
            $g = 0;
            $b = $x;
        }

        $r = ($r + $m) * 255;
        $g = ($g + $m) * 255;
        $b = ($b + $m) * 255;
        
        $r = $r < 0 ? 0 : $r;
        $g = $g < 0 ? 0 : $g;
        $b = $b < 0 ? 0 : $b;
        
        // Prepare the result
        $result = array(floor($r), floor($g), floor($b));
        
        // Store in cache
        self::_cacheSet(array($h, $s, $l), $result, self::CACHE_PREFIX_HSL_TO_RGB);
        
        // All done
        return $result;
    }

    /**
     * ### Basic methods
     */

    /**
     * Optimize JPEG or PNG images
     * 
     * @param string $imageSource      Source image path
     * @param string $imageDestination (optional) Save the image to another path; default <b>null</b>
     * @return boolean
     */
    public static function optimize($imageSource, $imageDestination = null) {
        // Not a valid file
        if (!is_file($imageSource)) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('Cannot optimize missing file "' . $imageSource . '"');
            return false;
        }
        
        // Get the basename
        $itemBasename = basename($imageSource);
        
        // Replace the file
        if (null === $imageDestination) {
            $imageDestination = $imageSource;
        }
        
        // Prepare the command
        $command = null;
        do {
            // A PNG file
            if (preg_match('%\.png$%i', $itemBasename)) {
                $command = 'pngquant --force --quality 55 --output ' . escapeshellarg($imageDestination) . ' ' . escapeshellarg($imageSource);
                break;
            }

            // A JPEG file
            if (preg_match('%\.jpe?g$%i', $itemBasename)) {
                $command = 'convert -define jpeg:dct-method=float -strip -interlace Plane -sampling-factor 4:2:0 -quality 70% ' . escapeshellarg($imageSource) . ' ' . escapeshellarg($imageDestination);
                break;
            }
        } while(false);

        // Valid file found
        if (null !== $command) {
            // Log it
            Log::check(Log::LEVEL_DEBUG) && Log::debug($command);

            // Execute it
            $result = shell_exec($command);
            
            // All done
            return (null !== $result);
        }
        
        // Different files
        if ($imageSource != $imageDestination) {
            copy($imageSource, $imageDestination);
        }
        
        // Nothing to do
        return true;
    }
    
    /**
     * Bulge
     * 
     * @example 
     * // Create a bulge in the middle of the image
     * {image}->bulge($image);
     * // Create a small bulge (20%) in the middle of the image
     * {image}->bulge($image,20);
     * // Create a small reverse bulge at 20%
     * {image}->bulge($image,-20);
     * // Set radius to 100px
     * {image}->bulge($image,null,100);
     * // Center the bulge at 200 (X),300 (Y)
     * {image}->bulge($image,null,null,200,300);
     * 
     * @param resource $resource
     * @param int $size -> bulge dimension; -100 to 100
     * @param int $radius -> bulge radius
     * @param int $x -> x position of bulge
     * @param int $y -> y position of bulge
     * @return resource
     * 
     * 
     */
    function bulge($resource, $size = null, $radius = null, $x = null, $y = null) {
        // Get the image width and height
        $width = imagesx($resource);
        $height = imagesy($resource);

        // Set a default x and y center of bulge
        $x = is_null($x) ? intval($width / 2) : intval($x);
        $y = is_null($y) ? intval($height / 2) : intval($y);
        if ($x == 0) {
            $x = 1;
        }
        if ($y == 0) {
            $y = 1;
        }

        // The size must be an integer
        $minWH = min($width, $height);
        $size = is_null($size) ? $minWH / 10 : intval($size);

        // Is it 0? No changes to make
        if ($size == 0) {
            return $resource;
        }

        // Use the reverse fish eye?
        $reverse = $size <= 0;

        // The max for the size is 100
        if (abs($size) > 100)
            $size = $reverse ? -100 : 100;

        // Set the radius
        $radius = is_null($radius) ? ($minWH / 2 - 2) : intval($radius);

        // Calculate the W coefficient
        $w = 0.0001 + 0.02 * abs($size / 100);

        // Set this for amplification
        $s = $radius / log($w * $radius + 1, 10);

        // Store the last point created
        $last_point = null;

        // Create a new image
        $canvas = $this->canvas($width, $height);

        // Allocate transparent
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);

        // Make the changes pixel by pixel
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                // Calculate the radius
                $r = sqrt(pow($x - $i, 2) + pow($y - $j, 2));

                // Inside the bubble?
                if ($r <= $radius) {
                    // Get the new radius
                    $fr = $s * log(1 + $w * $r, 10);

                    // Calculate the angle for the polar coordinates
                    $a = atan2(($i - $x), ($j - $y));

                    // Get the new carthesian X coordinate
                    $_i = intval($x + $fr * sin($a));

                    // Get the new carthesian Y coordonate
                    $_j = intval($y + $fr * cos($a));

                    // Get the current color
                    $color = $reverse ? imagecolorat($resource, $_i, $_j) : imagecolorat($resource, $i, $j);
                    if ($color === FALSE)
                        $color = $transparent;

                    // Use this variable to store the distance to the nearest set point
                    if (is_null($last_point)) {
                        $fr = 1;
                    } else {
                        $fr = intval(sqrt(pow($last_point[0] - $_i, 2) + pow($last_point[1] - $_j, 2))) + 1;
                    }

                    // Save the last point
                    if ($fr > 1) {
                        // Use an ellipse to fill extra space
                        if ($fr > $radius / 4) {
                            $fr = 1;
                        }

                        $reverse ?
                                imagefilledellipse($canvas, $i, $j, $fr, $fr + 1, $color) :
                                imagefilledellipse($canvas, $_i, $_j, $fr, $fr + 1, $color);
                    } else {
                        // No need to complicate things
                        $reverse ?
                                imagesetpixel($canvas, $i, $j, $color) :
                                imagesetpixel($canvas, $_i, $_j, $color);
                    }

                    // Save the last point
                    $last_point = array($_i > $width ? $width : $_i, $_j > $height ? $height : $_j);
                }
                // Set the default
                else {
                    // Leave the pixels untouched
                    imagecopy($canvas, $resource, $i, $j, $i, $j, 1, 1);

                    // Save the last point
                    $last_point = array($i, $j);
                }
            }
        }

        // Return the image
        return $canvas;
    }

    /**
     * Optimize an image to the required size
     * 
     * @param resource $img     Image resource
     * @param string   $path    Image storage path
     * @param int      $maxSize Maximum size in bytes
     */
    public function optimizeJpegSize($img, $path, $maxSize) {
        // Start from a quality of 95
        $qualityIncrement = 95;
        
        // Log this
        Log::check(Log::LEVEL_INFO) && Log::info('[Image Optimization] Analyzing "' . $path . '"...');
        
        // Save until it's in range
        while(true) {
            // Assert the size
            if (filesize($path) <= $maxSize) {
                // All done
                Log::check(Log::LEVEL_INFO) && Log::info('[Image Optimization] Image "' . $path . '" reached size lower than ' . $maxSize . ' bytes');
                break;
            }
            
            // Log this
            Log::check(Log::LEVEL_INFO) && Log::info('[Image Optimization] Saving JPEG at ' . $qualityIncrement . ' quality...');
            
            // Save the image
            $this->save($img, 'jpeg', $path, $qualityIncrement);
            
            // Reduce the quality by 5
            $qualityIncrement -= 5;
            
            // Quality has reached 0
            if ($qualityIncrement <= 0) {
                throw new Exception('Could not store the image at a size lower than ' . $maxSize . ' bytes.');
            }
        }
    }
    
    /**
     * Color
     * 
     * @param string $colorHex Color in Hexadecimal format
     * @return string Color name in English
     */
    public function getColorName($colorHex, $colorDefinitions = null) {
        // Prepare the result
        $result = "unknown";

        // Get the minimum value
        $minval = null;

        // Get the original color as RGB
        $originalColorRgb = array_values($this->hexToRgb($colorHex));
        
        // Get the color definitions
        if (null === $colorDefinitions) {
            $colorDefinitions = self::$colors;
        }
        
        // Go through the color definitions
        foreach ($colorDefinitions as $name => $knownColorRgb) {
            $dist = sqrt(pow($knownColorRgb[0] - $originalColorRgb[0], 2) + pow($knownColorRgb[1] - $originalColorRgb[1], 2) + pow($knownColorRgb[2] - $originalColorRgb[2], 2));
            if (null == $minval || $dist < $minval) {
                $minval = $dist;
                $result = $name;
            }
        }

        // Return the result
        return $result;
    }

    /**
     * Get the predominant colors in an image
     * 
     * @param string/array $imagePathOrDetails String (image location) or Array([resource, boolean - ninePatch])
     * @param int          $perThousand        Show colors above this "per thousand" ratio
     * @param int          $maxResults         Maximum number of items in the resulting array
     * @param int          $imgWidth           Resize the original image to this width to speed-up the process
     * @param int          $colorDistance      Color distance - used for color approximation
     * @param array        $colorDefinitions   Color definitions
     * @return array
     */
    public function getColors($imagePathOrDetails, $perThousand = 2, $maxResults = 5, $imgWidth = 30, $colorDistance = 30, $colorDefinitions = null) {
        if (is_array($imagePathOrDetails)) {
            list($image, $ninePatch) = $imagePathOrDetails;
        } else {
            // NinePatch?
            $ninePatch = (boolean) preg_match('%\.9\.png$%i', $imagePathOrDetails);

            // Load the image
            $image = $this->load($imagePathOrDetails);
        }
        
        // Compute the new height
        $imgHeight = intval(imagesy($image) * $imgWidth / imagesx($image));

        // Resize the image to the desired width
        $im = $this->resize($image, $imgWidth, $imgHeight);

        // Count the blank pixels
        $blankPixels = 0;
        
        // Unique colors
        $colorsArrayUnique = array();
        
        // Colors count
        $hexCount = array();
        
        // Go through each pixel one at a time
        for ($x = 0; $x < $imgWidth; $x++) {
            for ($y = 0; $y < $imgHeight; $y++) {
                // Nine-patch, ignore the borders
                if ($ninePatch) {
                    // Border
                    if ($x == 0 || $y == 0 || $x == $imgWidth - 1 || $y == $imgHeight - 1) {
                        // Ignore the border
                        continue;
                    }
                }
                
                // Get the index
                $index = imagecolorat($im, $x, $y);

                // Get the colors array
                $colors = imagecolorsforindex($im, $index);

                // Transparent pixel
                if ($colors['alpha'] >= 100) {
                    $blankPixels++;
                    continue;
                }

                // Assume unique
                $unique = true;
                    
                // Search for approximations
                foreach ($colorsArrayUnique as $uniqueColor) {
                    // Compute the distance
                    $distance = sqrt(
                        pow($colors['red'] - $uniqueColor[0], 2) + 
                        pow($colors['green'] - $uniqueColor[1], 2) + 
                        pow($colors['blue'] - $uniqueColor[2], 2)
                    );
                    
                    // Similar
                    if ($distance <= $colorDistance) {
                        // Not unique
                        $unique = false;
                        
                        // Overwrite the color
                        $colors = array(
                            'red' => $uniqueColor[0], 
                            'green' => $uniqueColor[1], 
                            'blue' => $uniqueColor[2]
                        );
                        
                        // Stop here
                        break;
                    }
                }
                
                // Unique color
                if ($unique) {
                    $colorsArrayUnique[] = array($colors['red'], $colors['green'], $colors['blue']);
                }
                
                // Get the hex value
                $hexValue = substr("0" . dechex($colors['red']), -2) . substr("0" . dechex($colors['green']), -2) . substr("0" . dechex($colors['blue']), -2);
                
                // Item set
                if (isset($hexCount[$hexValue])) {
                    $hexCount[$hexValue]++;
                } else {
                    $hexCount[$hexValue] = 1;
                }
            }
        }

        // Get the color name count
        $nameArray = array();
        foreach ($hexCount as $hex => $count) {
            $colorName = $this->getColorName($hex, $colorDefinitions);
            if (!isset($nameArray[$colorName])) {
                $nameArray[$colorName] = $count;
            } else {
                $nameArray[$colorName] += $count;
            }
        }
        natsort($nameArray);
        $nameArray = array_reverse($nameArray, true);

        // Prepare the result
        $result = array();
        foreach ($nameArray as $colorName => $times) {
            // Compute the "times per cent"
            $timesPerCent = $times / ($imgWidth * $imgHeight - $blankPixels) * 100;

            // Done
            if ($timesPerCent * 10 < $perThousand) {
                break;
            }

            // Save this color and the ratio
            if ('unknown' !== $colorName) {
                $result[] = array($colorName, round($timesPerCent, 2));
            }
        }

        // Maximum number of results
        $result = array_slice($result, 0, $maxResults);

        // Destroy the image
        imagedestroy($im);
        unset($im);
        
        // All done
        return $result;
    }

    /**
     * Get the predominant colors in an image
     * 
     * @param string/array $imagePathOrDetails String (image location) or Array([resource, boolean - ninePatch])
     * @param array        $colorDefinitions   Color definitions
     * @param int          $perThousand        Show colors above this "per thousand" ratio
     * @param int          $maxResults         Maximum number of items in the resulting array
     * @param int          $imgWidth           Resize the original image to this width to speed-up the process
     * @return array
     */
    public function getColorsHsl($imagePathOrDetails, $colorDefinitions, $perThousand = 0, $maxResults = 10, $imgWidth = 20) {
        // Call the cache
        if (null !== $cachedResult = self::_cacheGet($imagePathOrDetails, self::CACHE_PREFIX_COLORS_HSL)) {
            return $cachedResult;
        }
        
        if (is_array($imagePathOrDetails)) {
            list($image, $ninePatch) = $imagePathOrDetails;
        } else {
            // NinePatch?
            $ninePatch = (boolean) preg_match('%\.9\.png$%i', $imagePathOrDetails);

            // Load the image
            $image = $this->load($imagePathOrDetails);
        }
        
        // Compute the new height
        $imgHeight = intval(imagesy($image) * $imgWidth / imagesx($image));

        // Resize the image to the desired width
        $im = $this->resize($image, $imgWidth, $imgHeight);

        // Count the blank pixels
        $blankPixels = 0;
        
        // Get the colors identified
        $nameArray = array();
        
        // Go through each pixel one at a time
        for ($x = 0; $x < $imgWidth; $x++) {
            for ($y = 0; $y < $imgHeight; $y++) {
                // Nine-patch, ignore the borders
                if ($ninePatch) {
                    // Border
                    if ($x == 0 || $y == 0 || $x == $imgWidth - 1 || $y == $imgHeight - 1) {
                        // Ignore the border
                        continue;
                    }
                }
                
                // Get the index
                $index = imagecolorat($im, $x, $y);

                // Get the colors array
                $colors = imagecolorsforindex($im, $index);

                // Transparent pixel
                if ($colors['alpha'] >= 100) {
                    $blankPixels++;
                    continue;
                }
                
                // Get the HSL value
                $hsl = $this->rgbToHsl(array_values($colors));
                
                // Go through the color definitions
                foreach ($colorDefinitions as $colorName => $colorDefinition) {
                    // Get the hue range
                    if ($colorDefinition[0][0] > $colorDefinition[0][1]) {
                        $rangeH = array_merge(range($colorDefinition[0][0], 360), range(0, $colorDefinition[0][1]));
                    } else {
                        $rangeH = range($colorDefinition[0][0], $colorDefinition[0][1]);
                    }
                    
                    // Get the saturation range
                    $rangeS = range(intval(100 * $colorDefinition[1][0]), intval(100 * $colorDefinition[1][1]));
                    
                    // Get the value range
                    $rangeV = range(intval(100 * $colorDefinition[2][0]), intval(100 * $colorDefinition[2][1]));

                    // Identified color
                    if (in_array(round($hsl[0], 0), $rangeH) && in_array(intval(100 * $hsl[1]), $rangeS) && in_array(intval(100 * $hsl[2]), $rangeV)) {
                        // Initialize the counter
                        if (!isset($nameArray[$colorName])) {
                            $nameArray[$colorName] = 0;
                        }
                        
                        // Add the color
                        $nameArray[$colorName]++;
                    }
                }
            }
        }

        // Get the color name count
        natsort($nameArray);
        $nameArray = array_reverse($nameArray, true);

        // Prepare the result
        $result = array();
        foreach ($nameArray as $colorName => $times) {
            // Compute the "times per cent"
            $timesPerCent = $times / ($imgWidth * $imgHeight - $blankPixels) * 100;

            // Done
            if ($timesPerCent * 10 < $perThousand) {
                break;
            }

            // Save this color and the ratio
            if ('unknown' !== $colorName) {
                $result[] = array($colorName, round($timesPerCent, 2));
            }
        }

        // Maximum number of results
        $result = array_slice($result, 0, $maxResults);

        // Destroy the image
        imagedestroy($im);
        unset($im);

        // Save to cache
        self::_cacheSet($imagePathOrDetails, $result, self::CACHE_PREFIX_COLORS_HSL);
        
        // All done
        return $result;
    }

    /**
     * Pick a color from a certain point
     * 
     * @param resource $resource Image resource
     * @param int      $x        X coordinate
     * @param int      $y        Y coordinate
     * @return array RGBA
     */
    public function pick($resource, $x, $y) {
        // Get the index
        $index = imagecolorat($resource, $x, $y);

        // Get the colors array
        $colors = imagecolorsforindex($resource, $index);
        
        // Return the colors
        return array_values($colors);
    }
    
    /**
     * Do these colors match?
     * 
     * @param array $colorA RGB of color 1
     * @param array $colorB RGB of color 2
     * @return boolean True if they do, false if they don't
     */
    public static function colorMatch($colorA, $colorB, $goodDistance = 50) {
        // Get the distance
        $dist = sqrt(pow($colorA[0] - $colorB[0], 2) + pow($colorA[1] - $colorB[1], 2) + pow($colorA[2] - $colorB[2], 2));
        
        // Check if optimal
        return $dist <= $goodDistance;
    }
    
    /**
     * Create a blank canvas
     * 
     * @example 
     * // 200 by 300 pixels
     * {image}->canvas(200,300);
     * // 200 by 200 pixels
     * {image}->canvas(200);
     * 
     * @param int $width
     * @param int $height
     * @return resource
     */
    function canvas($width = null, $height = null, $transparency = 127) {
        // Stop if the GD extension is not load
        if (!in_array('gd', get_loaded_extensions())) {
            throw new Exception("You must enable the GD extension in order to perform image manipulations.");
        }

        // Prepare the width and height
        if (empty($width)) {
            throw new Exception("Please specify the image width.");
        } else {
            $width = intval($width);
        }
        if (empty($height)) {
            $height = $width;
        } else {
            $height = intval($height);
        }

        // Return the
        if (false === $resource = imagecreatetruecolor($width, $height)) {
            throw new Exception("Could not create a new blank canvas of ${width}x${height}.");
        }

        $transparent = @imagecolorallocatealpha($resource, 0, 0, 0, $transparency);
        imagesavealpha($resource, true);
        imagefill($resource, 0, 0, $transparent);

        return $resource;
    }

    /**
     * Converts given image to the specified format
     * 
     * @example
     * # Converts given image to gif, saves it as 'testImage.gif' and deletes 'testImage.jpeg'
     * $this->image->convert('testImage.jpeg','gif');
     * # Loads the file once, returns a resource; 'testImate.jpeg' is deleted
     * $this->image->convert('testImage.jpeg','png',FALSE);
     * # Lowers the saved image quality to 80
     * $this->image->convert('testImage.jpeg','png',NULL,NULL,80);
     * 
     * @param string $fileName
     * @param string $to
     * <ul>
     * <li>'png'</li>
     * <li>'gif'</li>
     * <li>'jpeg'</li>
     * </ul>
     * @param bool $saveToFile #! saves in class call file directory
     * @param bool $deleteOriginal #! delete source file?
     * @param int $quality #! gifs don't support quality adjustment
     * @return BOOLEAN or resource
     */
    public function convert($fileName, $to, $saveToFile = TRUE, $deleteOriginal = TRUE, $quality = 100) {
        // Valid request?
        if (isset($this->availableConv [$to])) {
            // Load the image
            $res = $this->load($fileName);

            // Get file extension
            $ext = substr($fileName, strrpos($fileName, '.') + 1);

            // Dynamic functions again
            $function = 'image' . trim(strtolower($to));

            // Change the filename
            $fileName = str_replace($ext, $to, $fileName);

            // Set the quality
            if (!is_int($this->availableConv [$to])) {
                $quality = NULL;
            } elseif ($quality > $this->availableConv [$to]) {
                $quality = $this->availableConv [$to];
            } elseif ($quality < 0) {
                $quality = 0;
            }

            // Output the file to server?
            if ($saveToFile === TRUE) {
                // The acual save function
                if (FALSE === $function($res, $fileName, $quality)) {
                    throw new Exception("Could not save file '" . $fileName . "'.");
                }

                // Delete the original file from the server?
                if ($deleteOriginal === TRUE) {
                    unlink(str_replace($to, $ext, $fileName));
                }

                // Clear the resource
                imagedestroy($res);
            } else {
                // Delete the original file from the server?
                if ($deleteOriginal === TRUE) {
                    unlink(str_replace($to, $ext, $fileName));
                }

                // Return the resource
                return $res;
            }
        } else {
            // Invalid request
            throw new Exception("Invalid image type '" . $to . "'.");
        }
    }

    /**
     * Crops an image from point 1 to point 2
     * 
     * @example 
     * # crop the image from (10,30) to (20,60)
     * $im = $this->image->crop($resource,10,30,20,60);
     * 
     * @param resource &$resource
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return resource
     */
    public function crop(&$resource, $x1, $y1, $x2, $y2, $erase = false) {
        // Figure out width and height of cropped section
        $w = abs(intval($x2) - intval($x1));
        $h = abs(intval($y2) - intval($y1));

        // Create empty holder
        $im = $this->canvas($w, $h);

        // This is important; treats inverted cropping (bottom right to top left)
        if ($x1 > $x2) {
            $aux = $x1;
            $x1 = $x2;
            $x2 = $aux;
        }
        if ($y1 > $y2) {
            $aux = $y1;
            $y1 = $y2;
            $y2 = $aux;
        }

        // Return the result
        if (false !== imagecopyresampled($im, $resource, 0, 0, $x1, $y1, $w, $h, $w, $h)) {
            // Need to remove that section
            if ($erase) {
                // No alpha blending
                imagealphablending($resource, false);

                // Create the new color
                $eraser = imagecolorallocatealpha($resource, 0, 0, 0, 127);
                
                // Go through the lines
                for($i = $x1; $i <= $x2; $i++) {
                    for ($j = $y1; $j <= $y2; $j++) {
                        // Erase the pixel
                        imagesetpixel($resource, $i, $j, $eraser);
                    }
                }
            }
            return $im;
        } else {
            throw new Exception("Imagecopyresized failed.");
        }
    }

    /**
     * Displays given image; $filename or $resource
     * 
     * @example 
     * # Send a local image to the browser
     * $this->image->display('testImage.jpeg');
     * # Or display an image resource
     * $this->image->display($resource);
     * # Set the displayed image's type; default = png
     * $this->image->display($resource,'jpeg');
     * # Display it as 'foo.bar'
     * $this->image->display($resource,null,'foo.bar');
     * 
     * @param string/resource $fileNameOrResource
     * @param string $resourceType
     * @param string $displayName
     * @return boolean
     */
    public function display($fileNameOrResource, $TypeOfResource = 'png', $displayName = null) {
        // Shall we commence?
        if (isset($fileNameOrResource)) {
            if (is_string($fileNameOrResource)) {
                // This is a file, then
                $r = $this->load($fileNameOrResource, TRUE);

                // Set the type and resource
                $type = $r ['t'];
                $resource = $r ['r'];

                // Set the display name
                if (is_null($displayName)) {
                    $fileNameOrResource = str_replace(array(DIRECTORY_SEPARATOR, '/'), DIRECTORY_SEPARATOR, $fileNameOrResource);
                    $displayName = substr($fileNameOrResource, strrpos($fileNameOrResource, DIRECTORY_SEPARATOR) + 1);
                }
            } elseif (is_resource($fileNameOrResource)) {
                // Set the type and resource
                $type = !is_null($TypeOfResource) ? trim(strtolower($TypeOfResource)) : 'png';

                // Deal with invalid image types
                if (!isset($this->availableConv [$type]))
                    $type = 'png';

                // Set the resource
                $resource = $fileNameOrResource;

                // Set the display name
                if (is_null($displayName)) {
                    $displayName = implode('-', octoms::$oms_url_segments) . '.' . $type;
                }
            } else {
                throw new Exception("The first argument must be either a String or a Resource.");
            }

            // Set the header accordingly
            header('Content-type: image/' . $type);
            header(sprintf('Content-Disposition: inline; filename="%s"', $displayName));

            // Output the image
            call_user_func('image' . $type, $resource);

            // Destroy the image
            imagedestroy($resource);
        } else {
            throw new Exception("You must specify a file name or resource to display.");
        }
    }

    /**
     * Apply a gaussian blur
     * 
     * @param resource &$resource Image resource
     * @param int      $amount    Amount
     */
    public function gaussianBlur(&$resource, $amount = 4) {
        // Cleanup the amount parameter
        $amount = intval($amount);
        $amount = $amount < 1 ? 1 : ($amount > 10 ? 10 : $amount);
        
        // Apply a gaussian blur
        for ($i = 1; $i <= $amount; $i++) {
            imageconvolution($resource, array(array(1.0, 2.0, 1.0), array(2.0, 4.0, 2.0), array(1.0, 2.0, 1.0)), 16, 0);
        }
    }

    /**
     * This an advanced multifilter and rotator
     * 
     * @example 
     * # Apply 3 filters, rotate the image 5 deg to the left
     * $this->image->filter($resource,'negate,colorize-200-10-10-0,emboss','5');
     * 
     * @param resource $resource
     * @param string $filters - apply one or more of the following filters
     * <ul>
     * <li>negate</li>
     * <li>grayscale</li>
     * <li>brightness-{level}</li>
     * <li>contrast-{level}</li>
     * <li>colorize-{red}-{green}-{blue}-{alpha? 0-255}</li>
     * <li>edgedetect</li>
     * <li>emboss</li>
     * <li>gaussian_blur</li>
     * <li>selective_blur</li>
     * <li>mean_removal</li>
     * <li>smooth-{level}</li>
     * </ul>
     * Example: 'negate,colorize-200-10-10-0,emboss'
     * @param string $rotAng
     * <ul>
     * <li>'-3'	-> minus 3 degrees (image rotates clockwise)</li>
     * <li>'+5'	-> plus 5 degrees (image rotates anticlockwise)</li>
     * <li>'10'	-> plus 10 degrees (equivalent to +10)</li>
     * </ul>
     * @throws Exception
     * @return resource
     */
    public function filter($resource, $filters = '', $rotAng = '-45') {
        // Are there any filters?
        if ($filters != '' && $filters != NULL) {
            // Apply the filters
            foreach ((array) explode(',', $filters) AS $filter) {
                // The filter must be a string
                if (!is_string($filter))
                    continue;

                // Do we recognize the filter?
                if (false === strpos($filter, '-')) {
                    // Get the filter name
                    $filter = 'IMG_FILTER_' . strtoupper($filter);

                    // The first argument is the resource
                    $parts = array($resource, constant($filter));
                } else {
                    // Get the filter name and components
                    $parts = explode('-', $filter);
                    $parts[0] = constant($filter = 'IMG_FILTER_' . strtoupper($parts[0]));

                    // The first argument is the resource
                    array_unshift($parts, $resource);
                }

                // Get the filter by calling a constant. #!
                if (defined($filter)) {
                    // Filter the image
                    if (FALSE === call_user_func_array('imagefilter', $parts)) {
                        throw new Exception("Could not apply image filter '{$filter}'.");
                    } else {
                        // All done; repopulate the resource
                        $resource = $parts[0];
                    }
                } else {
                    // Invalid filter
                    throw new Exception("Image filter '{$filter}' does not exist.");
                }
            }
        } # End of multifilter
        // Should we rotate this image?
        if (!empty($rotAng) && !is_null($rotAng)) {
            $resource = $this->rotate($resource, floatval(str_replace('+', '', $rotAng)));
        } # End of rotation
        // All done
        return $resource;
    }

    /**
     * Calculate an image edges (width of the space spaces on top, bottom, left and right)
     * 
     * @param resource $resource  Image resource
     * @param boolean  $ninePatch Image is a nine-patch
     * @return array('top' => int, 'bottom' => int, 'left' => int, 'right' => int)
     */
    public function getEdges($resource, $ninePatch = false) {
        // Get the image width and height
        $width = imagesx($resource);
        $height = imagesy($resource);
        
        // Prepare the edges
        $edges = array(
            'top'    => null,
            'bottom' => null,
            'left'   => null,
            'right'  => null,
        );
        
        // Empty columns and lines
        $emptyColumns = array();
        for ($i = 0; $i <= $width - 1; $i++) {
            // Assume column empty
            $empty = true;
            for ($j = 0; $j <= $height - 1; $j++) {
                // Nine-patch image
                if ($ninePatch && ($i == 0 || $j == 0 || $i == $width - 1 || $j == $height - 1)) {
                    // Ignore the border
                    continue;
                }
                
                // Get the color
                $rgba = imagecolorsforindex($resource, imagecolorat($resource, $i, $j));
                
                // Not entirely alpha
                if (127 !== $rgba['alpha']) {
                    $empty = false;
                    break;
                }
            }
            
            // Column is empty, mark
            if ($empty) {
                $emptyColumns[] = $i;
            }
        }
        
        // Empty lines
        $emptyLines = array();
        for ($j = 0; $j <= $height - 1; $j++) {
            // Assume column empty
            $empty = true;
            for ($i = 0; $i <= $width - 1; $i++) {
                // Nine-patch image
                if ($ninePatch && ($i == 0 || $j == 0 || $i == $width - 1 || $j == $height - 1)) {
                    // Ignore the border
                    continue;
                }
                
                // Get the color
                $rgba = imagecolorsforindex($resource, imagecolorat($resource, $i, $j));
                
                // Not entirely alpha
                if (127 !== $rgba['alpha']) {
                    $empty = false;
                    break;
                }
            }

            // Column is empty, mark
            if ($empty) {
                $emptyLines[] = $j;
            }
        }
        
        // Go through the columns
        if (in_array(0, $emptyColumns)) {
            foreach ($emptyColumns as $item) {
                if (0 == $item) {
                    $edges['left'] = 0;
                }
                
                if (null !== $edges['left'] && $edges['left'] + 1 == $item) {
                    $edges['left'] = $item;
                }
            }
        }
        if (in_array($width - 1, $emptyColumns)) {
            foreach (array_reverse($emptyColumns) as $item) {
                if ($width - 1 == $item) {
                    $edges['right'] = $width - 1;
                }
                
                if (null !== $edges['right'] && $edges['right'] - 1 == $item) {
                    $edges['right'] = $item;
                }
            }
        }
        
        // Go through the lines
        if (in_array(0, $emptyLines)) {
            foreach ($emptyLines as $item) {
                if (0 == $item) {
                    $edges['top'] = 0;
                }
                
                if (null !== $edges['top'] && $edges['top'] + 1 == $item) {
                    $edges['top'] = $item;
                }
            }
        }
        if (in_array($height - 1, $emptyLines)) {
            foreach (array_reverse($emptyLines) as $item) {
                if ($height - 1 == $item) {
                    $edges['bottom'] = $height - 1;
                }
                
                if (null !== $edges['bottom'] && $edges['bottom'] - 1 == $item) {
                    $edges['bottom'] = $item;
                }
            }
        }
        
        // Translate the edges into number of pixels
        $edges['top'] = null === $edges['top'] ? 0 : ($edges['top'] + 1);
        $edges['left'] = null === $edges['left'] ? 0 : ($edges['left'] + 1);
        $edges['bottom'] = null === $edges['bottom'] ? 0 : ($height - $edges['bottom']);
        $edges['right'] = null === $edges['right'] ? 0 : ($width - $edges['right']);
        
        // Nine-patch (ignored the border)
        if ($ninePatch) {
            foreach (array_keys($edges) as $key) {
                $edges[$key]--;
            }
        }
        
        // All done
        return $edges;
    }
    
    /**
     * Change the brightness of a color
     * 
     * @param array $rgbArray   RGB array
     * @param int   $brightness Brightness change
     * @return array
     */
    public function rgbBrightness($rgbArray, $brightness) {
        list($r, $g, $b) = array_values($rgbArray);

        if ($brightness > 255) {
            $brightness = 255;
        }
        
        if ($brightness < -255) {
            $brightness = -255;
        }
        
        $r = max(0, min(255, $r + $brightness));
        $g = max(0, min(255, $g + $brightness));  
        $b = max(0, min(255, $b + $brightness));

        return array($r, $g, $b);
    }
    
    /**
     * Apply real grayscale
     * 
     * @param resource $resource Resource
     */
    public function grayscale(&$resource) {
        // Get the dimensions
        $width = imagesx($resource);
        $height = imagesy($resource);
        
        // Get a canvas
        $canvas = $this->canvas($width, $height);
        
        // Colors ids
        $colorIds = array();
        
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                // Get the color
                $rgba = imagecolorsforindex($resource, imagecolorat($resource, $i, $j));
                
                // Get the mean to remove saturation
                $mean = round(($rgba['red'] + $rgba['green'] + $rgba['blue'])/3, 0);

                // Get the color id name
                $colorIdName = $mean . ':' . $rgba['alpha'];

                // Color ID set for the first time
                if (!isset($colorIds[$colorIdName])) {
                    // Create the new color
                    $colorIds[$colorIdName] = imagecolorallocatealpha($canvas, $mean, $mean, $mean, $rgba['alpha']);
                }

                // Set it in place
                imagesetpixel($canvas, $i, $j, $colorIds[$colorIdName]);
            }
        }
        
        // Save the resource as canvas
        $resource = $canvas;
    }
    
    /**
     * Apply a gradient overlay
     * 
     * @param resource &$resource   Image resource
     * @param array    $gradient    Array of RGB[A] arrays: R(red) 0-255, G(green) 0-255, B(blue) 0-255, A (alpha, optional) 0-1
     * @param boolean  $overlayMode Overlay mode (Image::BLEND_MODE_*)
     * @param boolean  $ninePatch   A nine-patch image
     * @param boolean  $ignoreAlpha Ignore the alpha channel
     */
    public function gradientOverlay(&$resource, Array $gradient, $overlayMode = self::BLEND_MODE_OVERLAY, $ninePatch = false, $ignoreAlpha = true) {
        // Not enough colors
        if (count($gradient) < 2) {
            throw new Exception('Not enought colors. At least 2 needed.');
        }

        // Prepare the steps array
        $stepsArray = array();
        
        // Gradient count
        $gradientCount = count($gradient);

        // Get the dimensions
        $width = imagesx($resource);
        $height = imagesy($resource);

        // Get a clone of the resource
        $resourceClone = $this->copy($resource);
        
        // Correct colors
        foreach ($gradient as $key => $color) {
            // Array values
            $color = array_values($color);
            
            // Assume alpha 1
            if (!isset($color[3])) {
                $color[3] = 1;
            }
            
            // Overwrite
            $gradient[$key] = $color;
        }

        // Create the steps
        for ($j = 0; $j <= $height-1; $j++) {
            // Index start
            $indexStart = intval($j / $height * ($gradientCount - 1));
            
            // Index end
            $indexEnd = $indexStart + 1;
            
            // Get the start color
            $colorStart = $gradient[$indexStart];
 
            // Get the end color
            $colorEnd = $gradient[$indexEnd];
            
            // Get the start offset
            $offsetStart = $indexStart * ($height - 1) / ($gradientCount - 1);
            
            // Get the end offset
            $offsetEnd = $indexEnd * ($height - 1) / ($gradientCount - 1);
            
            // Avoid division by 0
            if ($offsetEnd == $offsetStart) {
                $offsetEnd = $offsetStart + 1;
            }
            
            // Prepare the array
            $stepsArray[] = array(
                ($colorEnd[0] - $colorStart[0]) * ($j - $offsetStart) / ($offsetEnd-$offsetStart) + $colorStart[0],
                ($colorEnd[1] - $colorStart[1]) * ($j - $offsetStart) / ($offsetEnd-$offsetStart) + $colorStart[1],
                ($colorEnd[2] - $colorStart[2]) * ($j - $offsetStart) / ($offsetEnd-$offsetStart) + $colorStart[2],
                ($colorEnd[3] - $colorStart[3]) * ($j - $offsetStart) / ($offsetEnd-$offsetStart) + $colorStart[3],
            );
        }
        
        // Get a canvas
        $resource = $this->canvas($width, $height);
        
        // Colors ids
        $colorIds = array();
        
        // Apply the colors
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                // Get the original color
                $rgba = imagecolorsforindex($resourceClone, imagecolorat($resourceClone, $i, $j));
                
                // Get the overlay color
                $overlayColor = $stepsArray[$j];
                
                // Leave colors as they are
                list($red, $green, $blue, $alpha) = array_values($rgba);
                                
                do {
                    // Nine-patch image
                    if ($ninePatch && ($i == 0 || $j == 0 || $i == $width - 1 || $j == $height - 1)) {
                        // Nothing to do
                        break;
                    }
                    
                    // Transparent pixel
                    if (127 === $alpha && $ignoreAlpha) {
                        // Nothing to do
                        break;
                    }
                    
                    // Compute the new alpha
                    if (!$ignoreAlpha) {
                        $overlayAlpha = intval(127 - 127 * $overlayColor[3]);

                        // Get the new alpha
                        $alpha = min($alpha, round(($alpha + $overlayAlpha) / 2));
                    }
                
                    // Switch the overlay mode
                    switch ($overlayMode) {
                        // Overlay (and overlay desaturated) blending modes
                        case self::BLEND_MODE_OVERLAY:
                        case self::BLEND_MODE_DESATURATE:
                            // Compute the mean
                            $mean = round(($rgba['red'] + $rgba['green'] + $rgba['blue']) / 3);
                            
                            // Desaturate?
                            $desaturate = self::BLEND_MODE_DESATURATE == $overlayMode;
                            
                            // Get the channels
                            $red   = round($overlayColor[3] * ($overlayColor[0] - ($desaturate ? $mean : $rgba['red'])) / 2 + ($desaturate ? $mean : $rgba['red']));
                            $green = round($overlayColor[3] * ($overlayColor[1] - ($desaturate ? $mean : $rgba['green'])) / 2 + ($desaturate ? $mean : $rgba['green']));
                            $blue  = round($overlayColor[3] * ($overlayColor[2] - ($desaturate ? $mean : $rgba['blue'])) / 2 + ($desaturate ? $mean : $rgba['blue']));
                            break;
                        
                        // Normal blending mode
                        case self::BLEND_MODE_NORMAL:
                            // Get the channels
                            $red   = round($overlayColor[3] * ($overlayColor[0] - $rgba['red']) + $rgba['red']);
                            $green = round($overlayColor[3] * ($overlayColor[1] - $rgba['green']) + $rgba['green']);
                            $blue  = round($overlayColor[3] * ($overlayColor[2] - $rgba['blue']) + $rgba['blue']);
                            break;
                        
                        // Color blending mode
                        case self::BLEND_MODE_COLOR;
                            // Get the original HSL
                            $hslOrig = $this->rgbToHsl($rgba);
                            
                            // Get the overlay HSL
                            $hslOver = $this->rgbToHsl($overlayColor);
                            
                            // Get the channels
                            $hue = round($hslOrig[1] * ($hslOver[0] - $hslOrig[0]) + $hslOrig[0]);
                            
                            // Get the new RGB
                            list($red, $green, $blue) = $this->hslToRgb(array($hue, $hslOrig[1], $hslOrig[2]));
                            break;
                    }
                } while(false);
                    
                // Get the color id name
                $colorIdName = $red . ',' . $green . ',' . $blue . ':' . $alpha;

                // Color ID set for the first time
                if (!isset($colorIds[$colorIdName])) {
                    // Create the new color
                    $colorIds[$colorIdName] = imagecolorallocatealpha($resource, $red, $green, $blue, $alpha);
                }

                // Set it in place
                imagesetpixel($resource, $i, $j, $colorIds[$colorIdName]);
            }
        }
    }
    
    /**
     * Copy a resource
     * 
     * @param resource $resource
     * @return resource
     */
    public function copy($resource) {
        // Get a canvas
        $canvas = $this->canvas(imagesx($resource), imagesy($resource));
        
        // Copy the resource
        imagecopy($canvas, $resource, 0, 0, 0, 0, imagesx($resource), imagesy($resource));

        // All done
        return $canvas;
    }
    
    /**
     * Mask an image
     * 
     * @param resource $resource  Resource
     * @param resource $mask      Mask
     * @param boolean  $ninePatch The mask is a nine patch
     * @return resource
     * @throws Exception
     */
    public function mask($resource, $mask, $ninePatch = false) {
        // Get the width and height
        list($width, $height) = array(imagesx($resource), imagesy($resource));
        
        // Verify dimensions
        if ($width != imagesx($mask) || $height != imagesy($mask)) {
            throw new Exception('Both mask and original resource need to have the same dimensions');
        }
        
        // Prepare a canvas result
        $result = $this->canvas($width, $height);
        
        // Colors
        $colors = array();
        
        // Go through each pixel
        for ($i = 0; $i <= $width - 1; $i++) {
            for ($j = 0; $j <= $height - 1; $j++) {
                // Get the mask pixel
                $rgbaMask = imagecolorsforindex($mask, imagecolorat($mask, $i, $j));
                
                // Get the resource pixel
                $rgbaRes = imagecolorsforindex($resource, imagecolorat($resource, $i, $j));
                
                // Compute the alpha
                $alpha = $rgbaMask['alpha'] * ((127 - $rgbaRes['alpha']) / 127) + $rgbaRes['alpha'];

                // Red
                $red   = $rgbaRes['red'];

                // Green
                $green = $rgbaRes['green'];

                // Blue
                $blue  = $rgbaRes['blue'];
                
                // Nine-patch
                if ($ninePatch) {
                    // Border; use the mask color as is
                    if ($i == 0 || $i == $width-1 || $j ==0 || $j == $height - 1) {
                        // Alpha
                        $alpha = $rgbaMask['alpha'];

                        // Red
                        $red   = $rgbaMask['red'];

                        // Green
                        $green = $rgbaMask['green'];

                        // Blue
                        $blue  = $rgbaMask['blue'];
                    }
                }
                
                // Get the color name
                $colorName = $red . ',' . $green . ',' . $blue . ':' . $alpha;
                
                // Get the color
                if (!isset($colors[$colorName])) {
                    $colors[$colorName] = imagecolorallocatealpha($result, $red, $green, $blue, $alpha);
                }
                
                // Set it in place
                imagesetpixel($result, $i, $j, $colors[$colorName]);
            }
        }
        
        // All done
        return $result;
    }
    
    /**
     * Replace by color
     * 
     * @param resource $resource        Image resource
     * @param array    $findColorRgb    Find color RGB array
     * @param array    $replaceColorRgb Replace color RGB array
     * @param int      $fuzziness       Fuzziness
     * @param boolean  $ninePatch       A 9-patch image
     * @return null
     */
    public function colorReplace(&$resource, Array $findColorRgb, Array $replaceColorRgb, $fuzziness = 64, $ninePatch = false) {
        // Set the alpha blending to false (to place real pixels, including empty ones)
        imagealphablending($resource, false);
        
        // Array values
        $findColorRgb = array_values($findColorRgb);
        $replaceColorRgb = array_values($replaceColorRgb);
        
        // Set the alpha blending to false (to place real pixels, including empty ones)
        imagealphablending($resource, false);
        
        // Get the dimensions
        $width = imagesx($resource);
        $height = imagesy($resource);
        
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                // Nine-patch image
                if ($ninePatch && ($i == 0 || $j == 0 || $i == $width - 1 || $j == $height - 1)) {
                    // Ignore the border
                    continue;
                }
                
                // Get the color
                $color = imagecolorat($resource, $i, $j);
                $rgba = imagecolorsforindex($resource, $color);
                
                // Leave transparent pixels intact
                if (127 != $rgba['alpha']) {
                    // Get the color distance
                    $distance = sqrt(pow(($rgba['red'] - $findColorRgb[0]), 2) + pow(($rgba['green'] - $findColorRgb[1]), 2) + pow(($rgba['blue'] - $findColorRgb[2]), 2));
                    
                    // Allowable distance
                    if ($distance <= $fuzziness) {
                        // Get the HSL of the current color
                        $hslOrig = $this->rgbToHsl(array($rgba['red'], $rgba['green'], $rgba['blue']));
                        
                        // Get the HSL of the replacing color
                        $hslRepl = $this->rgbToHsl($replaceColorRgb);
                        
                        // Get the new color in rgb
                        $rgbNew = $this->hslToRgb(
                            array(
                                $hslRepl[0], 
                                ($hslOrig[1] + $hslRepl[1])/2, 
                                ($hslOrig[2] + $hslRepl[2])/2, 
                            )
                        );
                        
                        // Create the new color
                        $new = imagecolorallocatealpha($resource, $rgbNew[0], $rgbNew[1], $rgbNew[2], $rgba['alpha']);

                        // Set it in place
                        imagesetpixel($resource, $i, $j, $new);
                    }
                }
            }
        }
    }

    /**
     * Image information
     * 
     * @example 
     * // Get the info on this $resource
     * {image}->info($resource);
     * // What if we saved it as jpeg?
     * {image}->info($resource,'jpeg');
     * 
     * @param resource $resource
     * @param string $type - save type: png/jpeg/gif
     * @return array <ul>
     * <li>width - int, width in pixels</li>
     * <li>height - int, heigh in pixels</li>
     * <li>truecolor - boolean, wether the image is truecolor</li>
     * <li>size - int, the approximate image size in bytes</li>
     * </ul>
     */
    function info($resource, $type = 'png') {
        // Set the information array
        $imageInfo = array(
            'width' => imagesx($resource),
            'height' => imagesy($resource),
            'truecolor' => imageistruecolor($resource)
        );

        // Get the type
        $type = strtolower($type);
        if (!in_array($type, array_keys($this->availableConv)))
            $type = 'png';

        // Output it locally
        ob_start();
        call_user_func('image' . $type, $resource);
        $image = ob_get_clean();

        // Set the size
        $imageInfo['size'] = strlen($image);

        // Clean some of the memory
        unset($image);

        // Return the information
        return $imageInfo;
    }

    /**
     * Advanced Image Loader
     * 
     * @uses Used by display(), convert()
     * @example 
     * # Get the full information on the image
     * # $resource = array('r'=>$resource,'w'=>200,'h'=>300,'t'=>jpeg);
     * #'r' - image resource
     * #'w' - image width (in pixels)
     * #'h' - image height (in pixels)
     * #'t' - image type
     * $resource = $this->image->load('testImage.jpeg',TRUE);
     * # Or just load it as a resource
     * $resource = $this->image->load('testImage.jpeg');
     * 
     * @throws Exception
     * @param resource/string $fileName
     * @param boolean $advanced
     * @return array
     */
    public function load($fileName, $advanced = FALSE) {
        // Stop if the GD extension is not load
        if (!in_array('gd', get_loaded_extensions())) {
            throw new Exception("You must enable the GD extension in order to perform image manipulations.");
        }

        if (isset($fileName)) {
            // Format the filename
            $fileName = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $fileName);

            // Get the info
            if (FALSE === $arr = getimagesize($fileName)) {
                throw new Exception("No such file or directory '" . $fileName . "'.");
            }

            // Set the basic array
            $r['w'] = $arr [0];
            $r['h'] = $arr [1];
            $r['t'] = str_replace('image/', '', $arr ['mime']);

            // Is this image valid?
            if (isset($this->availableConv [$r['t']])) {
                // Load the image
                $r ['r'] = call_user_func('imagecreatefrom' . $r ['t'], $fileName);

                // Is it all ok?
                if (gettype($r['r']) != 'resource') {
                    throw new Exception("Could not create image resource from file '" . $fileName . "'.");
                } else {
                    // Save the alpha channel
                    imagesavealpha($r['r'], true);
                        
                    // Do we need to know more?
                    if ($advanced === TRUE) {
                        // Return the large array
                        return $r;
                    } else {
                        // Return the resource
                        return $r['r'];
                    }
                }
            } else {
                throw new Exception("File type '" . $r['t'] . "' not allowed.");
            }
        } else {
            // Not enough params
            throw new Exception("You must specify an image file to load.");
        }
    }

    /**
     * Slight variation of luminosity on the corners
     * 
     * @param resource $imageResource Image resource
     * @param array    $vectors       4-dimensional array of integers
     * @return resource Image resource
     */
    public function cornersShifting($imageResource, $vectors) {
        // Get the image dimensions
        $width = imagesx($imageResource);
        $height = imagesy($imageResource);
        
        // Get the 4 corners
        $corners = array(
            array(1, 1),
            array($width - 2, 1),
            array(1, $height - 2),
            array($width - 2, $height - 2),
        );
        
        // No alpha blending
        imagealphablending($imageResource, false);

        // Modify each corner
        foreach ($corners as $key => $pixelCoords) {
            // Get the % of luminosity shift
            $percent = $vectors[$key];
            
            // Active change needed
            if ($percent != 0) {
                // Get the color
                if (false != $color = @imagecolorat($imageResource, $pixelCoords[0], $pixelCoords[1])) {
                    // Get the RGBA
                    $rgba = imagecolorsforindex($imageResource, $color);

                    // Get the HSL
                    $hsl = $this->rgbToHsl(array($rgba['red'], $rgba['green'], $rgba['blue']));

                    // Percent cannot go anywhere
                    if ($hsl[2] == 0 && $percent < 0 || $hsl[2] == 1 && $percent > 0) {
                        $percent *= -1;
                    }

                    // Change the luminosity
                    $hsl[2] += $percent/100;

                    // Validate the luminosity
                    $hsl[2] = $hsl[2] < 0 ? 0 : ($hsl[2] > 1 ? 1 : $hsl[2]);

                    // Get the RGB
                    $rgb = $this->hslToRgb($hsl);

                    // Create the new color
                    $new = imagecolorallocatealpha($imageResource, $rgb[0], $rgb[1], $rgb[2], $rgba['alpha']);

                    // Set it in place
                    imagesetpixel($imageResource, $pixelCoords[0], $pixelCoords[1], $new);
                }
            }
        }
        
        // Return the image resource
        return $imageResource;
    }

    /**
     * Remove the padding pixels on a nine-patch image resource (that includes the borders)
     * 
     * @param resource $resource Image GD resource (a nine-patch with borders)
     * @param array    $edges    A result of Image::getEdges
     */
    public function ninePatchRemovePadding(&$resource, $edges) {
        // No alpha blending
        imagealphablending($resource, false);
        
        // Create the new color
        $eraser = imagecolorallocatealpha($resource, 0, 0, 0, 127);

        // Get the image width and height
        $width = imagesx($resource);
        $height = imagesy($resource);
        
        // Set it in place
        for ($i = 0; $i <= $width - 1; $i++) {
            for ($j = 0; $j <= $height - 1; $j++) {
                // Padding border
                if (
                    ($j == $height-1 && ($i <= $edges['left'] - 1 || $i >= $width - $edges['right'])) 
                    || 
                    ($i == $width-1 && ($j <= $edges['top'] - 1 || $j >= $height - $edges['bottom']))
                ) {
                    // Erase the pixel
                    imagesetpixel($resource, $i, $j, $eraser);
                }
            }
        }
    }
    
    /**
     * Change image opacity
     * 
     * @example 
     * // Reduce the opacity of an image to 50%
     * {image}->opacity($resource,127);
     * 
     * @param resource $resource
     * @param int $opacity - 0 = transparent, 255 = opaque
     * @param boolean $exceptBorder - Don't apply the effect on the border (for 9-patch files)
     * @throws Exception
     * @return resource $resource
     */
    function opacity($resource, $opacity = 255, $exceptBorder = false) {
        // Get the width and height of the resource
        $width = imagesx($resource);
        $height = imagesy($resource);

        // Set the opacity limits
        $opacity = intval($opacity);

        // Manage outrageous values
        $opacity = $opacity > 255 ? 255 : ($opacity < 0 ? 0 : $opacity);
        $opacity = ((~$opacity) & 0xff) >> 1;
        
        imagealphablending($resource, false);

        for ($y = 0; $y < ($height); $y++) {
            for ($x = 0; $x < ($width); $x++) {
                if ($exceptBorder) {
                    if ($x == 0 || $y == 0 || $x == $width-1 || $y == $height-1) {
                        continue;
                    }
                }
                // Get the color
                $color = imagecolorat($resource, $x, $y);
                $rgba = imagecolorsforindex($resource, $color);
                
                // Set the new Alpha channel
                $rgba['alpha'] += intval((127 - $rgba['alpha']) * $opacity / 127);

                // Create the new color
                $new = imagecolorallocatealpha($resource, $rgba['red'], $rgba['green'], $rgba['blue'], $rgba['alpha']);

                // Set it in place
                imagesetpixel($resource, $x, $y, $new);
            }
        }

        // All done
        return $resource;
    }

    /**
     * Flip an image
     * 
     * @param resource $resource  Image resource
     * @param boolean  $hv        True for horizontal, false for vertical
     * @param boolean  $ninePatch Nine-patch resource
     * @return resource Flipped image
     */
    public function flip($resource, $hv = true, $ninePatch = false) {
        // Create a clean canvas
        $canvas = $this->canvas($width = imagesx($resource), $height = imagesy($resource));
        
        // No alpha blending
        imagealphablending($canvas, false);
        
        // Go through each pixel
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                // Get the new coordinates
                $newCoordX = $hv ? $width - $i - 1 : $i;
                $newCoordY = $hv ? $j : $height - $j - 1;
                
                // Ignore border for nine-patch
                if ($ninePatch) {
                    if ($hv && ($i == 0 || $i == $width - 1)) {
                        $newCoordX = $i;
                    }
                    
                    if (!$hv && ($j == 0 || $j == $height - 1)) {
                        $newCoordY = $j;
                    }
                }
                
                // Get the color
                $color = imagecolorat($resource, $i, $j);
                $rgba = imagecolorsforindex($resource, $color);
                
                // Create the new color
                $new = imagecolorallocatealpha($canvas, $rgba['red'], $rgba['green'], $rgba['blue'], $rgba['alpha']);

                // Set it in place
                imagesetpixel($canvas, $newCoordX, $newCoordY, $new);
            }
        }
        
        // Return the canvas
        return $canvas;
    }

    /**
     * Overlays 2 images (image resources)
     * 
     * @example 
     * // Place the first image (50% opaque) over the second image
     * $res = $this->image->overlay($img1,$img2,'top-center',50);
     * // Place it at 10px X, 20px Y
     * $res = $this->image->overlay($img1,$img2,'10,20',50);
     * 
     * @param resource $overlay
     * @param resource $canvas
     * @param string $overlayPos
     * <ul>
     * <li>'top'</li>
     * <li>'bottom'</li>
     * <li>'left'</li>
     * <li>'center'</li>
     * <li>'right'</li>
     * <li>any CSS valid binomial combination like 'top-center' </li>
     * </ul>
     * <br/> OR <b>'x,y'</b>
     * @param int $overlayAlpha - 0(transparent) to 255(opaque)
     * @throws Exception
     * @return resource
     */
    public function overlay($overlay, $canvas, $overlayPos = 'center', $overlayAlpha = 255) {
        // Set opacity
        if ($overlayAlpha === NULL) {
            $overlayAlpha = 255;
        } else {
            // Integer, please
            $overlayAlpha = intval($overlayAlpha);

            // Manage outrageous values
            $overlayAlpha = $overlayAlpha > 255 ? 255 : ($overlayAlpha < 0 ? 0 : $overlayAlpha);
        }

        // Valid params, please
        if (isset($overlay) && isset($canvas)) {
            // Canvas width and height
            $c_w = imagesx($canvas);
            $c_h = imagesy($canvas);

            // overlay width and height
            $o_w = imagesx($overlay);
            $o_h = imagesy($overlay);

            // overlay opacity
            $o_o = intval($overlayAlpha);

            // Default overlay
            if (is_null($overlayPos))
                $overlayPos = 'center';

            // Use CSS-like positioning
            if (FALSE === strpos($overlayPos, ',')) {
                // Get the directives
                $exp = array_map('trim', explode('-', $overlayPos));

                // Hardcode these 1-word specs
                if (!isset($exp [1])) {
                    switch ($overlayPos) {
                        case 'left' :
                            $exp [0] = 'top';
                            $exp [1] = 'left';
                            break;
                        case 'center' :
                            $exp [0] = 'center';
                            $exp [1] = 'center';
                            break;
                        case 'right' :
                            $exp [0] = 'top';
                            $exp [1] = 'right';
                            break;
                        case 'top' :
                            $exp [0] = 'top';
                            $exp [1] = 'center';
                            break;
                        case 'bottom' :
                            $exp [0] = 'bottom';
                            $exp [1] = 'center';
                            break;
                    }
                }

                // Correct the word order
                if (!in_array($exp [0], array('top', 'center', 'bottom'))) {
                    // Switch the words
                    $aux = $exp [0];
                    $exp [0] = $exp [1];
                    $exp [1] = $aux;
                    unset($aux);
                }

                // Set position arrays
                $yPosArr = array('top' => 0, 'center' => 0.5, 'bottom' => 1);
                $xPosArr = array('left' => 0, 'center' => 0.5, 'right' => 1);

                // Figure out location of overlay
                if (isset($xPosArr [$exp [1]]) && isset($yPosArr [$exp [0]])) {
                    $cx = ($c_w - $o_w) * floatval($xPosArr [$exp [1]]);
                    $cy = ($c_h - $o_h) * floatval($yPosArr [$exp [0]]);
                } else {
                    // Bad Specs
                    throw new Exception("Your position specifications are bad ({$overlayPos}).");
                }
            }
            // Use exact positions
            else {
                list($cx, $cy) = explode(',', $overlayPos);
            }

            // Set the transparency
            if ($o_o < 255) {
                $overlay = $this->opacity($overlay, $o_o);
            }

            // Overlay the pics
            if (FALSE === $r = imagecopy($canvas, $overlay, $cx, $cy, 0, 0, $o_w, $o_h)) {
                throw new Exception("Could not apply watermark.");
            }

            // All done
            return $canvas;
        } else {
            // Resources were not specified
            throw new Exception("You must specify the 2 images to overlap.");
        }
    }

    /**
     * Apply a clipping mask
     * 
     * @param resource $resourceTop     Top resource
     * @param resource $resourceBottom  Bottom resource (mask)
     * @param int      $x               Starting X coordinate
     * @param int      $y               Starting Y coordinate
     * @param boolean  $ninePatch       Bottom resource is a 9-patch
     */
    public function clip($resourceTop, $resourceBottom, $x = 0, $y = 0, $ninePatch = false) {
        // Get the width and height for the bottom image
        $bottomWidth = imagesx($resourceBottom);
        $bottomHeight = imagesy($resourceBottom);
        
        // Get the width and height for the top image
        $topWidth = imagesx($resourceTop);
        $topHeight = imagesy($resourceTop);
        
        // Create the canvas
        $canvas = $this->canvas($bottomWidth, $bottomHeight);
        
        // Go through each pixel
        for ($i = 0; $i < $bottomWidth; $i++) {
            for ($j = 0; $j < $bottomHeight; $j++) {
                // Get the index
                $bottomIndex = imagecolorat($resourceBottom, $i, $j);

                // Get the colors array
                $bottomColors = imagecolorsforindex($resourceBottom, $bottomIndex);
                
                // For non-transparent pixels
                if (127 !== $bottomColors['alpha']) {
                    // Nine-patch, on the borders
                    if ($ninePatch && ($i == 0 || $j == 0 || $i == $bottomWidth - 1 || $j == $bottomHeight - 1)) {
                        // Get the bottom layer pixel
                        imagesetpixel($canvas, $i, $j, $bottomIndex);
                        
                        // Skip other verifications
                        continue;
                    }
                    
                    // Get the top layer pixel
                    if ($i >= $x && $i <= $topWidth && $j >= $y && $j <= $topHeight) {
                        // Get the color
                        $topIndex = imagecolorat($resourceTop, $i - $x, $j - $y);
                        
                        // Get the colors array
                        $topColors = imagecolorsforindex($resourceTop, $topIndex);
                        
                        // Set the actual color
                        $color = imagecolorallocatealpha($canvas, $topColors['red'], $topColors['green'], $topColors['blue'], $bottomColors['alpha']);
                        
                        // Set the pixel
                        imagesetpixel($canvas, $i, $j, $color);
                    } else {
                        // Get the bottom layer pixel
                        imagesetpixel($canvas, $i, $j, $bottomIndex);
                    }
                }
            }
        }
        
        // Return the canvas
        return $canvas;
    }
    
    /**
     * Perspective transormation
     * 
     * @example 
     * // Give the image some perspective
     * {image}->perspective($image,'0,0','80,20','80,80','0,100');
     * 
     * @param resource $resource
     * @param string $p1 - 'x,y' values
     * @param string $p2
     * @param string $p3
     * @param string $p4
     * @throws Exception
     * @return resource
     */
    function perspective($resource, $p1 = null, $p2 = null, $p3 = null, $p4 = null) {
        // Get the width and height
        $width = imagesx($resource);
        $height = imagesy($resource);

        // Verify the resource
        if (!$width || !$height) {
            throw new Exception("Please provide a valid resource.");
        }

        // Left, top
        if (FALSE !== strpos($p1, ',')) {
            list($x1, $y1) = explode(',', $p1);
        } else {
            if (!is_null($p1)) {
                $x1 = $p1;
                $y1 = $p1;
            } else {
                $x1 = 0;
                $y1 = 0;
            }
        }

        // Right, top
        if (FALSE !== strpos($p2, ',')) {
            list($x2, $y2) = explode(',', $p2);
        } else {
            if (!is_null($p2)) {
                $x2 = $p2;
                $y2 = $p2;
            } else {
                $x2 = $width;
                $y2 = 0;
            }
        }

        // Right, bottom
        if (FALSE !== strpos($p3, ',')) {
            list($x3, $y3) = explode(',', $p3);
        } else {
            if (!is_null($p3)) {
                $x3 = $p3;
                $y3 = $p3;
            } else {
                $x3 = $width;
                $y3 = $height;
            }
        }

        // Left, bottom
        if (FALSE !== strpos($p4, ',')) {
            list($x4, $y4) = explode(',', $p4);
        } else {
            if (!is_null($p4)) {
                $x4 = $p4;
                $y4 = $p4;
            } else {
                $x4 = 0;
                $y4 = $height;
            }
        }

        // Get the X and Y values separately
        $xValues = array();
        $yValues = array();
        
        for ($i = 1; $i <= 4; $i++) {
            $xValues[] = ${'x' . $i};
            $yValues[] = ${'y' . $i};
        }
        
        // Create a new canvas
        $canvas = $this->canvas(max($xValues) + 1, max($yValues) + 1);

        // Perform the replacements
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                // Get the color
                list($dst_x, $dst_y) = $this->_corPix($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4, $x, $y, $width, $height);
                $boxSize = ($y >= $height - 3 || $x >= $width - 3) ? 1 : 3;
                $boxPerc = ($y >= $height - 3 || $x >= $width - 3) ? 20 : 33;
                imagecopymerge($canvas, $resource, $dst_x, $dst_y, $x, $y, $boxSize, $boxSize, $boxPerc);
            }
        }

        // Destroy the resource
        imagedestroy($resource);

        // All done
        return $canvas;
    }

    /**
     * This function resizes given image and returns it as a resource
     *  
     * @example 
     * # The picture is scaled to width 300px and height 400px
     * $res = $this->image->resize($img,'300','400')
     * # The picture is scaled to 60%
     * $res = $this->image->resize($img,'60');
     * # The picture is scaled so that the final width will be 400px
     * $res = $this->image->resize($img,'width=400');
     * # The picture is scaled so that the final height will be 200px
     * $res = $this->image->resize($img,'height=200');
     * 
     * @param resource $resource
     * @param string/int $width
     * @param string/int $height
     * @param OR string $scale
     * @param OR string $toWidth
     * @param OR string $toHeight
     * @throws Exception
     * @param resource $mode
     */
    public function resize($resource, $params = null) {
        // Get the function arguments
        $args = func_get_args();

        // Get our image's sizes
        $resourceW = imagesx($resource);
        $resourceH = imagesy($resource);

        // Width and Height specified?
        if (count($args) == 3) {
            // We use integer widths and heights
            $width = intval($args [1]);
            $height = intval($args [2]);
        } elseif (count($args) == 2) {
            // Scale?
            if (strpos($args [1], '=') === FALSE) {
                // Get the scale
                $scale = intval($args [1]);

                // Set new dimensions
                $width = $resourceW * $scale / 100;
                $height = $resourceH * $scale / 100;
            } else {
                // Interpret the argument
                $exp = explode('=', $args [1]);

                // resize to width?
                if (trim($exp [0]) == 'width') {
                    $width = intval(trim($exp [1]));
                    $height = intval($width * $resourceH / $resourceW);
                } // resize to height?
                elseif (trim($exp [0]) == 'height') {
                    $height = intval(trim($exp [1]));
                    $width = intval($height * $resourceW / $resourceH);
                }
            }
        }

        // Create the holder
        $holder = $this->canvas($width, $height);

        // Perform some magic
        if (FALSE === $r = imagecopyresampled($holder, $resource, 0, 0, 0, 0, $width, $height, $resourceW, $resourceH)) {
            // Something went wrong
            throw new Exception("Could not perform imagecopyresampled.");
        }
        return $holder;
    }

    /**
     * Rotate an image
     * 
     * @example
     * // Rotate an image to the right by 30 degrees
     * {image}->rotate($imageResource,30);
     * 
     * @param resource $resource Image GD resource
     * @param int      $angle    Angle
     * @return resource type GD
     */
    function rotate($resource, $angle) {
        // Get ImageMagick
        $imageMagick = new ImageMagick();
        
        // Prepare a canvas
        $canvas = $this->copy($resource);
        
        // Rotate
        $imageMagick->rotateResource($canvas, $angle);
        
        // All done
        return $canvas;
    }
    
    /**
     * Save an image resource under given filename
     * 
     * @example 
     * # Save the image resource locally as a jpeg file named "testImage.jpg"
     * $this->image->save($resource,'jpeg','testImage.jpg');
     * # Save the jpeg image at 80% quality; default = 100
     * $this->image->save($resource,'jpeg','img.jpg',80);
     * 
     * @param resource   $resource
     * @param string     $type
     * @param string     $fileName
     * @param string/int $quality
     * @return boolean
     */
    public function save($resource, $type, $fileName, $quality = 100) {
        // Are all arguments in place?
        if (isset($resource) && isset($type) && isset($fileName)) {
            if (isset($this->availableConv [strtolower($type)])) {
                // Set the function name
                $function = 'image' . trim(strtolower($type));

                // Set the quality
                if (!is_int($this->availableConv[$type])) {
                    $quality = NULL;
                } elseif ($quality > $this->availableConv[$type]) {
                    $quality = $this->availableConv [$type];
                } elseif ($quality < 0) {
                    $quality = 0;
                }
                
                // Save to file
                if (FALSE === $res = $function($resource, $fileName, $quality)) {
                    throw new Exception("Could not save the image '" . $fileName . "'.");
                }
            } else {
                // Unsupported image type
                throw new Exception("Unsupported image type '" . $type . "'.");
            }
        } else {
            // Not enough arguments
            throw new Exception("Not enough arguments.");
        }
    }

    /**
     * This function stacks up an undetermined number of images
     *
     * @example 
     * # Notice this method can stack up an unlimited number of images
     * $img = $this->image->stack(NULL,$img1,$img2,$img3,$img4);
     * 
     * @param strimg $mode
     * <ul>
     * <li>'vertical' - the function stacks up images vertically</li>
     * <li>'horizontal' - the function stacks up images horizontally</li>
     * </ul>
     * @param resource $img1
     * @param resource $img2
     * @param resource ...
     * @param resource $img_n
     * @throws Exception
     * @return resource
     */
    public function stack($mode = 'vertical', $img1, $img2) {
        // This function handles an undetermined number of arguments/images
        $args = func_get_args();
        $no_imgs = count($args) - 1;

        // Insufficient arguments?
        if ($no_imgs < 2) {
            throw new Exception("You must specify at least 2 images to stack.");
        }

        // Rewrite the default mode
        if (!is_string($args [0])) {
            $mode = 'vertical';
        } elseif ($args [0] != 'vertical' && $args [0] != 'horizontal') {
            // Invalid mode
            throw new Exception("Invalid stack mode. 'vertical' and 'horizontal' allowed.");
        }

        // Process all images
        for ($i = 1; $i < $no_imgs; $i++) {
            // Image1
            $dw = imagesx($args [$i]);
            $dh = imagesy($args [$i]);

            // Image2
            $sw = imagesx($args [$i + 1]);
            $sh = imagesy($args [$i + 1]);

            if ($mode == 'vertical') {
                // Create the holder
                $holder = $this->canvas($dw > $sw ? $dw : $sw, $dh + $sh);

                // Copy first image onto holder
                if (FALSE === $r = imagecopymerge($holder, $args [$i], 0, 0, 0, 0, $dw, $dh, 100)) {
                    throw new Exception("Imagecopymerge failed.");
                }

                // Copy second image onto holder
                if (FALSE === $r = imagecopymerge($holder, $args [$i + 1], 0, $dh, 0, 0, $sw, $sh, 100)) {
                    throw new Exception("Imagecopymerge failed.");
                }
            } elseif ($mode == 'horizontal') {
                // Create the holder
                $holder = $this->canvas($dw + $sw, $dh > $sh ? $dh : $sh );

                // Copy first image onto holder
                if (FALSE === $r = imagecopymerge($holder, $args [$i], 0, 0, 0, 0, $dw, $dh, 100)) {
                    throw new Exception("Imagecopymerge failed.");
                }

                // Copy second image onto holder
                if (FALSE === $r = imagecopymerge($holder, $args [$i + 1], $dw, 0, 0, 0, $sw, $sh, 100)) {
                    throw new Exception("Imagecopymerge failed.");
                }
            }
            // Good, now replace the second image with the holder
            $args [$i + 1] = $holder;

            // Free-up memory
            unset($holder);
        }

        // Return the last holder
        return $args [$i];
    }

    /**
     * Creates an image resource from given text
     * 
     * @example 
     * # An image of 'hello, world!' written in black, 20px high, using GD font 1
     * $img = $this->image->text('hello, world!','#000000','20',1);
     * # An image of 'hello, world!' written in black at default height of font 'testfont.gdf'
     * $img = $this->image->text('hello, world!','#000000',null,'testfont.gdf');
     * # An image of 'hello, world!' written in default (black) at default height of default font
     * $img = $this->image->text('hello, world!');
     * 
     * @param string $text
     * @param string $color
     * @param string $size
     * @throws Exception
     * @return resource
     */
    public function text($text, $color = null, $size = null, $font = null) {
        // Stop if the GD extension is not load
        if (!in_array('gd', get_loaded_extensions())) {
            throw new Exception("You must enable the GD extension in order to perform image manipulations.");
        }

        // Analyze the string
        $text = trim($text);
        $textLength = strlen($text);

        // Set the color
        if ($color == NULL || $color == '') {
            $color = $this->defaulttextColor;
        }

        // Custom font?
        if ($font != NULL) {
            if (strpos($font, '.') !== FALSE) {
                $font = imageloadfont($font);
                if ($font === FALSE) {
                    $font = $this->defaultFont;
                }
            } else {
                $font = intval($font);
            }
        } else {
            $font = $this->defaultFont;
        }

        // Set image parameters
        $width = $textLength * imagefontwidth($font);
        $height = imagefontheight($font);

        // Sizes in pixels
        if ($size == null || $size == '') {
            $size = $height;
        } else {
            $size = intval($size);
        }

        // Create empty drawing
        $textImage = $this->canvas($width, $height);

        // Get the RGB Array
        $c = $this->hexToRgb($color);
        $b = $this->hexToRgb($this->defaulttextBg [0]);

        // Allocate transparent white; this automatically loads as background
        $bg = imagecolorallocate($textImage, $b [0], $b [1], $b [2]);
        $bg = imagecolortransparent($textImage, $bg);

        // Set the color
        $color = imagecolorallocate($textImage, $c [0], $c [1], $c [2]);

        // Create the image
        if (FALSE === $res = imagestring($textImage, $font, 0, 0, $text, $color)) {
            throw new Exception("Could not create an image string.");
        }

        // Let's scale it
        $textImage = $this->resize($textImage, 'height=' . $size);

        // All done!
        return $textImage;
    }

    /**
     * Tile an image
     * 
     * @example 
     * // Tile an image in a 400by400 canvas
     * {image}->tile($image,400);
     * // Tile an image in a 400by800 canvas
     * {image}->tile($image,400,800);
     * 
     * @param resource $resource - the tile
     * @param int $width - canvas width
     * @param int $height - canvas height
     * @throws Exception
     * @return resource
     */
    function tile($resource = NULL, $width = NULL, $height = NULL) {
        // Verify the resource
        if (is_null($resource) || FALSE === imagesx($resource)) {
            throw new Exception("You must provide a valid image resource to tile.");
        }

        // Format the width and height
        if (is_null($width)) {
            throw new Exception("You must provide a width.");
        } else {
            $width = intval($width);
        }
        if (is_null($height)) {
            $height = $width;
        } else {
            $height = intval($height);
        }

        // Create a 200x200 image
        if (FALSE === $canvas = $this->canvas($width, $height)) {
            throw new Exception("Try using a smaller image or increasing the PHP memory limit.");
        }

        // Set alpha blending to true
        imagealphablending($canvas, true);

        // Set the tile
        if (FALSE === imagesettile($canvas, $resource)) {
            throw new Exception("Could not set the tile.");
        }

        // Make the image repeat
        if (FALSE === imagefilledrectangle($canvas, 0, 0, $width, $height, IMG_COLOR_TILED)) {
            throw new Exception("Imagefilledrectangle failed.");
        }

        // All done
        return $canvas;
    }


    /**
     * Convert an image resource from truecolor (24bit) to 8bit
     * 
     * // 8bit to 24bit
     * {image}->truecolor($resource);
     * // 24bit to 8bit
     * {image}->truecolor($resource,false);
     * 
     * @param resource $resource
     * @param boolean $truecolor
     * @return resource
     */
    function truecolor($resource, $truecolor = true) {
        // Convert to truecolor
        if ($truecolor) {
            if (@imageistruecolor($resource)) {
                return $resource;
            }

            // Get the image's sizes
            $sizeX = imagesx($resource);
            $sizeY = imagesy($resource);
            
            // Create a blank canvas
            $new = $this->canvas($sizeX, $sizeY);

            // Save the resource
            if (FALSE === imagecopyresampled($new, $resource, 0, 0, 0, 0, $sizeX, $sizeY, $sizeX, $sizeY)) {
                throw new Exception("Could not convert the image to " . ($truecolor ? '24bit' : '8bit') . ".");
            }

            // Free up some memory
            imagedestroy($resource);

            // Return the resource
            return $new;
        }
        
        // The image is already a palette type
        if (!imageistruecolor($resource)) {
            return $resource;
        }

        // Try to convert to palette
        if (FALSE === imagetruecolortopalette($resource, false, 256)) {
            throw new Exception("Could not convert image to 8bit.");
        }
        
        return $resource;
    }
    
    /**
     * ### Helpers
     */
    protected function _corPix($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $x, $y, $SX, $SY) {
        return $this->_intersectLines(
            (($SY - $y) * $x0 + ($y) * $x3) / $SY, 
            (($SY - $y) * $y0 + $y * $y3) / $SY, 
            (($SY - $y) * $x1 + ($y) * $x2) / $SY, 
            (($SY - $y) * $y1 + $y * $y2) / $SY, 
            (($SX - $x) * $x0 + ($x) * $x1) / $SX, 
            (($SX - $x) * $y0 + $x * $y1) / $SX, 
            (($SX - $x) * $x3 + ($x) * $x2) / $SX, 
            (($SX - $x) * $y3 + $x * $y2) / $SX
        );
    }

    protected function _det($a, $b, $c, $d) {
        return $a * $d - $b * $c;
    }
    
    protected function _intersectLines($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) {
        $d = $this->_det($x1 - $x2, $y1 - $y2, $x3 - $x4, $y3 - $y4);
        if ($d == 0) {
            $d = 1;
        }
        
        $px = $this->_det($this->_det($x1, $y1, $x2, $y2), $x1 - $x2, $this->_det($x3, $y3, $x4, $y4), $x3 - $x4) / $d;
        $py = $this->_det($this->_det($x1, $y1, $x2, $y2), $y1 - $y2, $this->_det($x3, $y3, $x4, $y4), $y3 - $y4) / $d;
        
        return array($px, $py);
    }

}

/*EOF*/