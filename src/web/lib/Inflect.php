<?php
/**
 * Theme Warlock - Inflect
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Inflect {

    /**
     * Plural definitions
     *
     * @var string[]
     */
    public static $plural = array(
        '(quiz)'                      => '$1zes',
        '^(ox)'                       => '$1en',
        '([m|l])ouse'                 => '$1ice',
        '(matr|vert|ind)ix|ex'        => '$1ices',
        '(media|info(rmation)?|news)' => '$1',
        '(c)hild'                     => '$1hildren',
        '(p)erson'                    => '$1eople',
        '(m)an'                       => '$1en',
        '([ieu]s|[ieuo]x)'            => '$1es',
        '([cs]h)'                     => '$1es',
        '(ss)'                        => '$1es',
        '([aeo]l)f'                   => '$1ves',
        '([^d]ea)f'                   => '$1ves',
        '(ar)f'                       => '$1ves',
        '([nlw]i)fe'                  => '$1ves',
        '([aeiou]y)'                  => '$1s',
        '([^aeiou])y'                 => '$1ies',
        '([^o])o'                     => '$1oes',
        '(phot|log|vide)o'            => '$1os',
        '(x|ch|ss|sh)'                => '$1es',
        '([^aeiouy]|qu)y'             => '$1ies',
        '(hive)'                      => '$1s',
        '(?:([^f])fe|([lr])f)'        => '$1$2ves',
        '(shea|lea|loa|thie)f'        => '$1ves',
        'sis'                         => 'ses',
        '([ti])um'                    => '$1a',
        '(tomat|potat|ech|her|vet)o'  => '$1oes',
        '(bu)s'                       => '$1ses',
        '(alias)'                     => '$1es',
        '(octop)us'                   => '$1i',
        '(ax|test)is'                 => '$1es',
        '(us)'                        => '$1es',
        's'                           => 'ses',
        '(.)'                         => '$1s',
    );
    
    /**
     * Singular definitions
     *
     * @var string[] 
     */
    static $singular = array(
        '/(quiz)zes$/i' => "$1",
        '/(matr)ices$/i' => "$1ix",
        '/(vert|ind)ices$/i' => "$1ex",
        '/^(ox)en$/i' => "$1",
        '/(alias)es$/i' => "$1",
        '/(octop|vir)i$/i' => "$1us",
        '/(cris|ax|test)es$/i' => "$1is",
        '/(shoe)s$/i' => "$1",
        '/(o)es$/i' => "$1",
        '/(bus)es$/i' => "$1",
        '/([m|l])ice$/i' => "$1ouse",
        '/(x|ch|ss|sh)es$/i' => "$1",
        '/(m)ovies$/i' => "$1ovie",
        '/(s)eries$/i' => "$1eries",
        '/([^aeiouy]|qu)ies$/i' => "$1y",
        '/([lr])ves$/i' => "$1f",
        '/(tive)s$/i' => "$1",
        '/(hive)s$/i' => "$1",
        '/(li|wi|kni)ves$/i' => "$1fe",
        '/(shea|loa|lea|thie)ves$/i' => "$1f",
        '/(^analy)ses$/i' => "$1sis",
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => "$1$2sis",
        '/([ti])a$/i' => "$1um",
        '/(n)ews$/i' => "$1ews",
        '/(h|bl)ouses$/i' => "$1ouse",
        '/(corpse)s$/i' => "$1",
        '/(us)es$/i' => "$1",
        '/s$/i' => ""
    );
    
    /**
     * Irregular definitions
     *
     * @var string[]
     */
    static $irregular = array(
        'move'             => 'moves',
        'foot'             => 'feet',
        'goose'            => 'geese',
        'sex'              => 'sexes',
        'child'            => 'children',
        'man'              => 'men',
        'tooth'            => 'teeth',
        'person'           => 'people',
        'valve'            => 'valves',
        'phenomenon'       => 'phenomena',
        'metamorphosis'    => 'metamorphoses',
        'tarantula'        => 'tarantulae',
        'octopus'          => 'octopi',
        'cyclops'          => 'cyclopes',
        'mantis'           => 'mantes',
        'hyrax'            => 'hyraces',
        'hippopotamus'     => 'hippopotami',
        'fish'             => 'fishes',
        'iris'             => 'irides',
        'sphinx'           => 'sphinges',
        'nebula'           => 'nebulae',
        'aura'             => 'aurae',
        'cry'              => 'cryings',
        'supernova'        => 'supernovae',
        'aurora'           => 'aurorae',
        'cactus'           => 'cacti',
        'fungus'           => 'fungi',
        'galea'            => 'galeae',
        'hypha'            => 'hyphae',
        'cortina'          => 'cortinae',
        'callus'           => 'calli',
        'ascus'            => 'asci',
        'paraphysis'       => 'paraphyses',
        'sorus'            => 'sori',
        'annulus'          => 'annuli',
        'capitulum'        => 'capitula',
        'rachis'           => 'rhachises',
        'thyrse'           => 'therses',
        'spadix'           => 'spadices',
        'pinna'            => 'pinnae',
        'involucre'        => 'involucra',
        'soma'             => 'somata',
        'halfpenny'        => 'halfpence',
        'penny'            => 'pence',
        'apomixis'         => 'apomixes',
        'autocatalysis'    => 'autocatalyses',
        'bacteriostasis'   => 'bacteriostases',
        'catalysis'        => 'catalyses',
        'ecchymosis'       => 'ecchymoses',
        'ellipsis'         => 'ellipses',
        'hemolysis'        => 'hemolyses',
        'lymphopoiesis'    => 'lymphopoieses',
        'lysis'            => 'lyses',
        'meiosis'          => 'meioses',
        'metastasis'       => 'metastases',
        'metathesis'       => 'metatheses',
        'morphallaxis'     => 'morphallaxes',
        'palingenesis'     => 'palingeneses',
        'peristalsis'      => 'peristalses',
        'symphysis'        => 'symphyses',
        'synapsis'         => 'synapses',
        'modulus'          => 'moduli',
        'extremum'         => 'extrema',
        'pul'              => 'puli',
        'centesimo'        => 'centesimi',
        'bolivar'          => 'bolivares',
        'drachma'          => 'drachmae',
        'haler'            => 'haleru',
        'eyrir'            => 'aurar',
        'leu'              => 'lei',
        'lev'              => 'leva',
        'stotinka'         => 'stotkini',
        'lira'             => 'lire',
        'loti'             => 'maloti',
        'sente'            => 'listente',
        'pfennig'          => 'pfennige',
        'markka'           => 'markkaa',
        'penni'            => 'pennia',
        'kroon'            => 'krooni',
        'sent'             => 'senti',
        'santims'          => 'santimi',
        'litas'            => 'litu',
        'paisa'            => 'paise',
        'likuta'           => 'makuta',
        'grosz'            => 'groszy',
        'obolus'           => 'oboli',
        'rotl'             => 'artel',
        'entasis'          => 'entases',
        'uncus'            => 'unci',
        'helix'            => 'helices',
        'rhombus'          => 'rhombi',
        'polyhedron'       => 'polyhedra',
        'caput'            => 'capita',
        'columella'        => 'columellae',
        'stria'            => 'striae',
        'stemma'           => 'stemmata',
        'tetrahedron'      => 'tetrahedra',
        'pentahedron'      => 'pentahedra',
        'octahedron'       => 'octahedra',
        'icosahedron'      => 'icosahedra',
        'trapezohedron'    => 'trapezohedra',
        'umbra'            => 'umbrae',
        'penumbra'         => 'penumbrae',
        'hypnosis'         => 'hypnoses',
        'aspergillosis'    => 'aspergilloses',
        'anthrax'          => 'anthraces',
        'stenosis'         => 'stenoses',
        'arteriosclerosis' => 'arterioscleroses',
        'atherosclerosis'  => 'atheroscleroses',
        'sclerosis'        => 'scleroses',
        'hemoptysis'       => 'hemoptyses',
        'avitaminosis'     => 'avitaminoses',
        'ranula'           => 'ranulae',
        'dermatosis'       => 'dermatoses',
        'exostosis'        => 'exostoses',
        'adenoma'          => 'adenomata',
        'angioma'          => 'angiomata',
        'chondroma'        => 'chondromata',
        'glioma'           => 'gliomata',
        'enchondroma'      => 'enchondromata',
        'fibroma'          => 'fibromata',
        'granuloma'        => 'granulomata',
        'gumma'            => 'gummata',
        'lipoma'           => 'lipomata',
        'lymphoma'         => 'lymphomata',
        'carcinoma'        => 'carcinomata',
        'adenocarcinoma'   => 'adenocarcinomata',
        'myoma'            => 'myomata',
        'myxoma'           => 'myxomata',
        'neuroma'          => 'neuromata',
        'rhabdomyoma'      => 'rhabdomyomata',
        'osteoma'          => 'osteomata',
        'papilloma'        => 'papillomata',
        'epithelioma'      => 'epitheliomata',
        'melanoma'         => 'melanomata',
        'zoonosis'         => 'zoonoses',
        'trauma'           => 'traumata',
        'petechia'         => 'petechiae',
        'diastasis'        => 'diastases',
        'hernia'           => 'herniae',
        'diverticulum'     => 'diverticula',
        'edema'            => 'oedemata',
        'hematoma'         => 'hematomata',
        'encephalitis'     => 'encephalitides',
        'lymphangitis'     => 'lymphangitides',
        'thrombus'         => 'thrombi',
        'embolus'          => 'emboli',
        'neurosis'         => 'neuroses',
        'psychosis'        => 'psychoses',
        'myiasis'          => 'myiases',
        'milieu'           => 'milieux',
        'miasma'           => 'miasmata',
        'diathesis'        => 'diathses',
        'sequela'          => 'sequelae',
        'scotoma'          => 'scotomata',
        'paralysis'        => 'paralyses',
        'ptosis'           => 'ptoses',
        'varix'            => 'varices',
        'borax'            => 'boraces',
        'alkali'           => 'alkalies',
        'fecula'           => 'feculae',
        'magma'            => 'magmata',
        'menstruum'        => 'menstrua',
        'tophus'           => 'tophi',
        'continuum'        => 'continua',
        'ephemera'         => 'ephemerae',
        'bicentennial'     => 'bicennaries',
        'tempo'            => 'tempi',
        'interregnum'      => 'interregna',
    );
    
    /**
     * Uncountable definitions
     *
     * @var string[]
     */
    static $uncountable = array(
        'sheep',
        'fish',
        'deer',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment',
        'bowling'
    );

    /**
     * Pluralize a noun
     * 
     * @param string $string Noun
     * @return string Plural form of noun
     */
    public static function pluralize($string) {
        // Save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable)) {
            return $string;
        }

        // Check for irregular singular forms
        foreach (array(self::$irregular, self::$plural) as $dataStore) {
            foreach ($dataStore as $pattern => $result) {
                if (preg_match('%' . $pattern . '$%i', $string)) {
                    return preg_replace('%' . $pattern . '$%i', $result, $string);
                }
            }
        }

        // Nothing to do
        return $string;
    }

    /**
     * Singularize a noun
     * 
     * @param string $string Noun
     * @return string Singular form of noun
     */
    public static function singularize($string) {
        // Save some time in the case that singular and plural are the same
        if (in_array(strtolower($string), self::$uncountable)) {
            return $string;
        }

        // Check for irregular plural forms
        foreach (self::$irregular as $result => $pattern) {
            $pattern = '/' . $pattern . '$/i';

            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // Check for matches using regular expressions
        foreach (self::$singular as $pattern => $result) {
            if (preg_match($pattern, $string)) {
                return preg_replace($pattern, $result, $string);
            }
        }

        // Nothing to do
        return $string;
    }
}

/* EOF */