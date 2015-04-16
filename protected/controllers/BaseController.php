<?php
class BaseController extends CController {

    const MSG_ERROR = 'error';
    const MSG_SUCCESS ='success';
    const MSG_NOTICE = 'notice';

    protected $messages = array();
    protected $request = null;
    public $layout = '';
    public $breadcrumbs = array();
    public $language = null;
    public $lang_name = null;
    public $baseUrl = null;
    public $imageUrl = null;
    public $siteUrl = null;
    public $absolutePath = null;
    public $mainImagesabsuPath = null;

    public $sysCurrStoreId = null ;
    public $local_id = null;

    public function __construct($id,$module= null){

        Yii::app()->charset =  'UTF-8';
        $this->layout = 'main';
        parent::__construct($id,$module);


        $this->request = Yii::app()->request;
        $this->setPageTitle('PA | Tours4Fun');

        /* URL management */
        $this->baseUrl = Yii::app()->baseUrl;
        $this->imageUrl = Yii::app()->baseUrl.'/img/';
        /* URL management*/

        $this->breadcrumbs = new Breadcrumbs();

        $this->absolutePath = realpath('./').DIRECTORY_SEPARATOR;
    }
    /**
    /**
     * sso token generate , it's Diego's algorithm
     * fix: 2012.11.30 remove appkey with microsecont as Diego's suggest
     */
    protected function createToken($email){
        $email = trim(strtolower($email));
        $clientip = Yii::app()->request->getUserHostAddress();
        $secret_key = Yii::app()->params['secret_key'];

        list($usec, $sec) = explode(" ", microtime());
        $token_timestamp=((float)$usec + (float)$sec);

        return sha1($email.$secret_key.$clientip.$token_timestamp).$token_timestamp;
    }
    /**
     * sso token generate , it's Diego's algorithm
     * fix: 2012.11.30 remove appkey with microsecont as Diego's suggest
     * fix: add timeout check
     */
    protected function validateToken($token,$email){
        if($token == ''||$email==''){
            return false;
        }

        list($usec, $sec) = explode(" ", microtime());
        $now_timestamp=((float)$usec + (float)$sec);
        $token_timestamp = floatval(substr($token,40));
        $timeout = intval( Yii::app()->params['sso_timeout']);
        if($now_timestamp - $token_timestamp > $timeout){
            return false;
        }

        $email = trim(strtolower($email));
        $encrypted = substr($token , 0 , 40);

        $clientip = Yii::app()->request->getUserHostAddress();
        $secret_key = Yii::app()->params['secret_key'];

        return sha1($email.$secret_key.$clientip.$token_timestamp) == $encrypted ;
    }
    /**
     * Display error message
     * @param int $fadeOut 0,no fade .how many second to fade this message ,no effect to error message.
     * @param int $type null ,for all type,BaseController::MSG_ERROR or other type
     */
    public function displayMessage($fadeOut= 0 , $type = null){
        $html = '';$js = '';
        $id = uniqid('msg');
        $jsAnimate = '.animate({opacity: 1.0}, '.$fadeOut.'000).slideUp("fast");';

        //display all flash
        $flashMessages = Yii::app()->user->getFlashes();
        if ($flashMessages) {
            foreach($flashMessages as $key => $message) {
                $html .= '<div class="'.$key.'box" id="'.$id.'_flash_'.$key.'"><h2>'.ucwords($key).'</h2><p>'.$message.'</p></div>';
                $js.= 'jQuery("#'.$id.'_flash_'.$key.'")'.$jsAnimate;
            }
        }

        if(($type == null || $type == self::MSG_ERROR) && !empty($this->messages[self::MSG_SUCCESS])){
            $html .= '<div class="successbox" id="'.$id.'_success"><h2>Successful</h2><p>';
            foreach($this->messages[self::MSG_SUCCESS] as $msg){
                $html .= $msg['message'].'<br />' ;
            }
            $html.="</div>";
            $js.= 'jQuery("#'.$id.'_success")'.$jsAnimate;
        }

        if(($type == null || $type == self::MSG_NOTICE) && !empty($this->messages[self::MSG_NOTICE])){
            $html .= '<div class="noticebox" id="'.$id.'_notice"><h2>Notice</h2><p>';
            foreach($this->messages[self::MSG_NOTICE] as $msg){
                $html .= $msg['message'].'<br />' ;
            }
            $html.="</div>";
            $js.= 'jQuery("#'.$id.'_notice")'.$jsAnimate;
        }

        if(($type == null || $type == self::MSG_ERROR) && !empty($this->messages[self::MSG_ERROR])){
            $html .= '<div class="errorbox" id="'.$id.'_error"><h2>Error</h2><p>';
            foreach($this->messages[self::MSG_ERROR] as $msg){
                $html .= $msg['message'].'<br />' ;
            }
            $html.="</div>";
            //$js.= 'jQuery("#'.$id.'_error")'.$jsAnimate; //dont remove error msg
        }
        if($html!= ''){
            echo $html;
            if($fadeOut > 0 && $js != ''){
                echo '<script type="text/javascript">'.$js.'</script>';
            }
        }
    }
    /**
     * change model state
     */
    protected function changeModelState($modelName , $pkValue ,$fieldName ,$new_state = null){
        $model = CActiveRecord::model($modelName)->findByPk($pkValue);
        if($model == null){
            $msg = $modelName.' not exists .';
        }
        if($msg == ''){
            $old_state = $model->getAttribute($fieldName);
            $new_state = $new_state == null? ($model->getAttribute($fieldName) == 0? 1:0):$old_state;

            $model->setAttribute($fieldName, $new_state);

            if($model->update(array($fieldName)))
                $curr_state = $new_state ;
            else
                $curr_state = $old_state;
        }

        if($this->request->isAjaxRequest){
            if($msg!=''){
                echo $msg;
            }else{
                echo $curr_state;
            }
        }
    }


    /**
     * Add Error Message
     * @param string $message
     * @param string $field
     * @author vincent.mi@toursforfun.com (2012-2-14)
     */
    public function addErrorMessage($message,$field = ''){
        $this->addMessage($message , self::MSG_ERROR,$field);
    }

    /**
     * Display a Message , In same request , if you want
     * message display in different request ,please use Yii::app()->user->setFlash
     * you can pass CModel to $message this method will pickout error.
     * @param mix $msg
     */
    public function addMessage($message='',$type=self::MSG_SUCCESS,$field=''){
        $type = strtolower($type);
        if(is_object($message)){
            if(is_subclass_of($message, 'CModel')){
                if($message->hasErrors()){
                    $type = self::MSG_ERROR;
                    if(!is_array($this->messages[$type])){
                        $this->messages[$type] = array();
                    }
                    foreach($message->getErrors() as $key=>$error){
                        $this->messages[$type][] = array('message'=>$error[0],'field'=>$key);
                    }
                }
            }else if(is_subclass_of($message, 'Exception')){
                $type = self::MSG_ERROR;
                if(!is_array($this->messages[$type])){
                    $this->messages[$type] = array();
                }
                $this->messages[$type][] = array('message'=>$message->getMessage(),'field'=>$key);
            }
        }else if($message != ''){
            if(!is_array($this->messages[$type])){
                $this->messages[$type] = array();
            }
            $this->messages[$type][] = array('message'=>$message,'field'=>$field);
        }
    }
    /**
     * add a flash message ,flash message was saved in sesssion.
     * @param string $type MSG_SUCCESS
     * @param string $message
     */
    public function addFlash($message,$type = self::MSG_SUCCESS,$clear = false){
        if($clear == false){
            $old_msg = Yii::app()->user->getFlash($type,'');
            if($old_msg!=''){
                $message = $old_msg.'<br/>'.$message;
            }
        }
        Yii::app()->user->setFlash($type,$message);
    }

    public function processOutput($output){

        if(!headers_sent()){
            header('Content-type: text/html;charset=utf-8');
        }

        return parent::processOutput($output);
    }
}
