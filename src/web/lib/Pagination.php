<?php
/**
 * Theme Warlock - Pagination
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Pagination {

    /**
     * _GET key used for pagination
     * 
     * @var string
     */
    protected $_getKey = 'p';
    
    /**
     * Data set
     * 
     * @var array
     */
    protected $_dataSet = array();
    
    /**
     * Items per page
     * 
     * @var int
     */
    protected $_itemsPerPage = 20;
    
    /**
     * Pagination
     * 
     * @param array $data         Items to paginate
     * @param int   $itemsPerPage (optional) Items per page; default 20
     * @param int   $key          (optional) _GET key used for pagination; default 'p'
     */
    public function __construct(Array $data, $itemsPerPage = 20, $key = 'p') {
        // Store the data set
        $this->setData($data);
        
        // Set the number of items per page
        $this->setItemsPerPage($itemsPerPage);
        
        // Store the GET key
        $this->setKey($key);
    }
    
    /**
     * Store the Pagination data
     * 
     * @param array $data
     * @return Pagination
     */
    public function setData(Array $data) {
        $this->_dataSet = $data;
        
        // All done
        return $this;
    }
    
    /**
     * Set the size of the page; defaults to 20
     * 
     * @param int $itemsPerPage
     * @return Pagination
     */
    public function setItemsPerPage($itemsPerPage) {
        // Numeric value
        $itemsPerPage = intval($itemsPerPage);
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 20;
        }
        
        // Store the value
        $this->_itemsPerPage = $itemsPerPage;
        
        // All done
        return $this;
    }
    
    /**
     * Get the size of the page
     * 
     * @return int
     */
    public function getItemsPerPage() {
        return $this->_itemsPerPage;
    }
    
    /**
     * Set the _GET key used for pagination; defaults to 'p'
     * 
     * @param string $getKey String with no spaces (a-z0-9_)
     * @return Pagination
     */
    public function setKey($getKey) {
        // Clean string
        $getKey = trim(preg_replace('%\W+%', '', $getKey));
        
        // Store the value
        $this->_getKey = strlen($getKey) ? $getKey : 'p';
        
        // All done
        return $this;
    }
    
    /**
     * Get the _GET key used for pagination
     * 
     * @return string
     */
    public function getKey() {
        return $this->_getKey;
    }
    
    /**
     * Get the data for the current page
     * 
     * @return array
     */
    public function getPageData() {
        // Add the placeholders
        return array_slice($this->_dataSet, ($this->getPageNumber() - 1) * $this->getItemsPerPage(), $this->getItemsPerPage());
    }
    
    /**
     * Get the current page number
     * 
     * @return int
     */
    public function getPageNumber() {
        // Get the page number
        $pageNumber = isset($_GET[$this->getKey()]) ? intval($_GET[$this->getKey()]) : 1;
                
        // Lower limit
        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        
        // Upper limit
        if ($pageNumber > $totalNoPages = $this->getTotalPages()) {
            $pageNumber = $totalNoPages;
        }
        
        // All done
        return $pageNumber;
    }
    
    /**
     * Get the total number of pages
     * 
     * @return int
     */
    public function getTotalPages() {
        return intval(ceil(count($this->_dataSet) / $this->getItemsPerPage()));
    }
    
    /**
     * Get the pagination HTML
     * 
     * @return string
     */
    public function getPaginationHtml() {
        // Prepare the result
        $result = '';
        
        // Pagination HTML needed
        if ($this->getTotalPages() > 1) {
            /// Prepare the holder
            $result .= '<div class="pagination-holder"><div class="btn-group">';
            
            // Add the pages
            for ($pageNumber = 1; $pageNumber <= $this->getTotalPages(); $pageNumber++) {
                $result .= '<a class="btn btn-default '. ($this->getPageNumber() == $pageNumber ? 'active' : '') . '" href="?' . $this->getKey() . '=' . $pageNumber . '" type="button">' . $pageNumber . '</a>';
            }
            
            // Close the holder
            $result .= '</div></div>';
        }
        
        // All done
        return $result;
    }

}

/* EOF */
