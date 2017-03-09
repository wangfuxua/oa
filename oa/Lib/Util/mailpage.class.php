<?
import("ORG.Util.Page");
class mailpage extends Page {
	
    public function __construct($totalRows,$listRows='',$parameter='')
    {
        parent::__construct($totalRows,$listRows,$parameter);
    }
	public function show($isArray=false){

        if(0 == $this->totalRows) return;
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;

        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
        	$allurl=$url."&".C('VAR_PAGE')."=".$upRow;
            $upPage="[<a href='#' onclick='javascript:$(\"#mail-act\").load(\"$allurl\")'>".$this->config['prev']."</a>]";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
        	$allurl=$url."&".C('VAR_PAGE')."=".$downRow;
            $downPage="[<a href='#' onclick='javascript:$(\"#mail-act\").load(\"$allurl\")'>".$this->config['next']."</a>]";
        }else{
            $downPage="";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $allurl1=$url."&".C('VAR_PAGE')."=".$preRow;
            $allurl2=$url."&".C('VAR_PAGE')."=1";
            $prePage = "[<a href='#' onclick='javascript:$(\"#mail-act\").load(\"$allurl1\")' >上".$this->rollPage."页</a>]";
            $theFirst = "[<a href='#' onclick='javascript:$(\"#mail-act\").load(\"$allurl2\")' >".$this->config['first']."</a>]";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $allurl1=$url."&".C('VAR_PAGE')."=".$nextRow;
            $allurl2=$url."&".C('VAR_PAGE')."=".$theEndRow;
                        
            $nextPage = "[<a href='#' onclick='javascript:$(\"#mail-act\").load(\"$allurl1\")' >下".$this->rollPage."页</a>]";
            $theEnd = "[<a href='#' onclick='javascript:$(\"#mail-act\").load(\"$allurl2\")' >".$this->config['last']."</a>]";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                	$allurl=$url."&".C('VAR_PAGE')."=".$page;
                    $linkPage .= "&nbsp;<a href='#' onclick='javascript:$(\"#mail-act\").load(\"$allurl\")'>&nbsp;".$page."&nbsp;</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= " [".$page."]";
                }
            }
        }
        $pageStr = '共'.$this->totalRows.' '.$this->config['header'].'/'.$this->totalPages.'页 '.$upPage.' '.$downPage.' '.$theFirst.' '.$prePage.' '.$linkPage.' '.$nextPage.' '.$theEnd;
        if($isArray) {
            $pageArray['totalRows'] =   $this->totalRows;
            $pageArray['upPage']    =   $url.'&'.C('VAR_PAGE')."=$upRow";
            $pageArray['downPage']  =   $url.'&'.C('VAR_PAGE')."=$downRow";
            $pageArray['totalPages']=   $this->totalPages;
            $pageArray['firstPage'] =   $url.'&'.C('VAR_PAGE')."=1";
            $pageArray['endPage']   =   $url.'&'.C('VAR_PAGE')."=$theEndRow";
            $pageArray['nextPages'] =   $url.'&'.C('VAR_PAGE')."=$nextRow";
            $pageArray['prePages']  =   $url.'&'.C('VAR_PAGE')."=$preRow";
            $pageArray['linkPages'] =   $linkPage;
            $pageArray['nowPage'] =   $this->nowPage;
            return $pageArray;
        }
        return $pageStr;
        		
		
		
		
	}
	
	
	
	
	
}


?>