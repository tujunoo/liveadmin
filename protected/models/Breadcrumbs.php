<?php
class Breadcrumbs
{
    private $breadcrumbs = array();
    public $separator = ' &rsaquo; ';
    function __construct()
    {
    }

    /**
     * reset breadcrumb
     *
     * @return
     */
    function reset()
    {
        $this->breadcrumbs = array();
    }

    /**
     * Add breadcrumb item.
     *
     * @param string $name
     * @param url    $url
     * @param int    $postion
     */
    function add($name, $url='', $postion = null)
    {
        if (is_array($url) && count($url) > 0) {
            $route = $url[0] ; unset($url[0]);
            $this->breadcrumbs[] =
                array($name, Yii::app()->urlManager->createUrl($route, $url));
        } else {
            if ( $url != '') {
                $this->breadcrumbs[] = array($name, $url);
            } else {
                $this->breadcrumbs[] = array($name);
            }
        }
    }

    /**
     * Modify breadcrumb item.
     *
     * @param mix     $name
     * @param string  $url
     * @param boolean $searchName
     * @param int     $limit
     * @author vincent.mi@toursforfun.com (2012-4-23)
     * @return
     */
    function edit($name , $url ,$searchName=true ,$limit = 0)
    {
        $m = 0 ;
        foreach ($this->breadcrumbs as $key=>$item) {
            if ($searchName == true && $item[0] == $name) {
                $this->breadcrumbs[$key][1] = $url;
                $m++;
                if ($limit != 0 && $m >= $limit) {
                    break;
                }
            } elseif ($searchName == false && $key == $name) {
                $this->breadcrumbs[$key][1] = $url;
                $m++;
                if ($limit != 0 && $m >= $limit) {
                    break;
                }
            }
        }
    }
	public function removeLast() {
		array_pop($this->breadcrumbs);
	}

    /**
     * Display breadcrumbs.
     *
     * @return
     */
    function display()
    {
        $baseUrl = Yii::app()->baseUrl ;
        $htmlParts = array();
        foreach ($this->breadcrumbs as $item) {
            list($name ,$url) = $item;
            if (empty($url)) {
                $htmlParts[] = $name ;
            } else {
                $htmlParts[] = '<a href="'.$url.'">'.$name.'</a>';
            }
        }
        echo implode($this->separator, $htmlParts);
    }
}
?>
