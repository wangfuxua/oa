<?php

/*[ Name: xmltree.php ] */
/*[ Author: Steve Hamilton ] */
/*[ WWW: http://www.phphelper.net ] */
/*[ Version: 1.0.1 ] */
/*[ Created: 07/11/2002 ] */
/*[ GPL:
    xmltree.php - PHP Class for file uploading
    Copyright (C) 2001 Steve Hamilton & phphelper.net
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

		http://www.gnu.org/licenses/gpl.txt
	]
*/

class tree {
  var $ROOT_IMAGE;
  var $PARA_ID;
  var $PARA_VALUE;
  var $file = "display.xml"; // Default file name
  var $fontSize = -1; // Default font size
  var $expandAll = 0; // 1 = yes, 0 = no

  var $imgPath = "/oa/Tpl/default/Public/images/treeicon/";
  // Image type array. Determined in NODE paramater "type" in xml
  // ie <node Name="Node3" Type="pdf"> would show the image associated
  // with pdf if it were an ending node(leaf)
  // "leaf" is the default leaf image
  var $imgType = array("leaf" => "t_leaf.gif",
                       "tplus" => "t_tplus.gif",
                       "cplus" => "t_cplus.gif",
                       "tminus" => "t_tminus.gif",
                       "cminus" => "t_cminus.gif",
                       "mbar" => "t_bar.gif",
                       "mtee" => "t_tee.gif",
                       "mcorner" => "t_c.gif",
                       "mspace" => "t_dot.gif",


                       "audio" => "t_audio.gif",
                       "disk" => "t_disk.gif",
                       "document" => "t_web.gif",
                       "email" => "t_email.gif",
                       "help" => "t_help.gif",
                       "ini" => "t_ini.gif",
                       "music" => "t_music.gif",
                       "pdf" => "t_pdf.gif",
                       "text" => "t_text.gif",
                       "folder"=> "t_folder.gif",
                       "web" => "t_web.gif",
                       "word" => "t_word.gif",
                       "write" => "t_write.gif"
                       );

  // Internal variables. No need to edit these
  var $xmlDepth = array();
  var $treeDepth;
  var $lastElement;
  var $nodeName;
  var $nodeCount ;
  var $maxLevel;

//----------------------------------------------------------------------------------------
  function startElement($parser, $name, $attrs) {
    $this->lastElement = $name;
    if($name=="TREE"){
      $this->node[0]["treeDepth"]="0";
      $this->node[0]["NAME"]=trim($attrs["NAME"]);
      $this->node[0]["TYPE"]=trim($attrs["TYPE"]);
      $this->node[0]["LINK"]=trim($attrs["LINK"]);
      $this->node[0]["TARGET"]=trim($attrs["TARGET"]);
      $this->node[0]["FORM"]=trim($attrs["FORM"]);
    }
    if($name=="NODE"){
      $this->treeDepth++;
      if($this->treeDepth > $this->maxLevel){
        $this->maxLevel = $this->treeDepth;
      }
      $this->nodeCount++;
      $this->nodeID = $this->nodeCount;
      $this->node[$this->nodeID]["treeDepth"] = $this->treeDepth;

    }
    while (list($k, $v) = each($attrs)) {
      $this->node[$this->nodeID][$k] = trim($v);
    }
    $this->xmlDepth[$parser]++;
  } // end startElement($parser, $name, $attrs)

  function dataElement($parser, $data) {
    $this->node[$this->nodeID][$this->lastElement] .= trim($data);
  } // end dataElement($parser, $data)

  function endElement($parser, $name) {
    if($name=="NODE"){
      $this->treeDepth--;
      $this->node[$this->nodeID]["last"]=1;
    }
    $this->xmlDepth[$parser]--;
  } // endElement($parser, $name)


//----------------------------------------------------------------------------------------
  function makeTree($XML_FILE = ""){
  		
		if(get_magic_quotes_runtime()){
			set_magic_quotes_runtime (0);
			$mq = 1;
		}
    $this->parser = xml_parser_create();
    xml_set_object($this->parser, &$this);
    xml_set_element_handler($this->parser, "startElement", "endElement");
    xml_set_character_data_handler ( $this->parser, "dataElement");
    if($XML_FILE){
      if (!($fp = fopen($XML_FILE, "r"))) {
        die("Could not open XML file");
      }
    }
    else{
      if (!($fp = fopen($this->file, "r"))) {
        die("Could not open XML file");
      }
    }

    while ($data = fread($fp, 4096)) {
      if (!xml_parse($this->parser, $data, feof($fp))) {
        die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($this->parser)),
                    xml_get_current_line_number($this->parser)));
      }
    }
    xml_parser_free($this->parser);
		if($mq){
			set_magic_quotes_runtime (1);
		}
    return $this->displayTree();

  } // end makeTree()


//----------------------------------------------------------------------------------------
  function makeTreeText($XML_TEXT = ""){
  		//echo $XML_TEXT;
  		
		if(get_magic_quotes_runtime()){
			set_magic_quotes_runtime (0);
			$mq = 1;
		}
    $this->parser = xml_parser_create();
    xml_set_object($this->parser, &$this);
    xml_set_element_handler($this->parser, "startElement", "endElement");
    xml_set_character_data_handler ( $this->parser, "dataElement");

    if($XML_TEXT=="")
    {
        die("XML TEXT is empty!");
    }

    $POS=0;
    $LEN=strlen($XML_TEXT);
    //echo $LEN;
    while ($data = substr($XML_TEXT,$POS, 4096)) {
      $data=str_replace("\0","/0",$data);
      $data=str_replace("&","&amp;",$data);
      if (!xml_parse($this->parser, $data, 0)) {
        die(sprintf("XML error: %s at line %d",
                    xml_error_string(xml_get_error_code($this->parser)),
                    xml_get_current_line_number($this->parser)));
      }

      $POS+=4096;
      if($POS>=$LEN)
         break;
    }
    xml_parser_free($this->parser);
		if($mq){
			set_magic_quotes_runtime (1);
		}
    return $this->displayTree();

  } // end makeTree()

//----------------------------------------------------------------------------------------
  function displayTree(){
    $e=$_REQUEST['e'];
    $expands = explode(",",$e);
    $totalExpands = count($expands);
    // Set branches to expand and make visible
    foreach($expands as $x){
      $expand[$x] = 1;
    }

    // First force depth 0 branches to be visible. Defined as <tree> tag in xml
    $visible[0]=1;
    for($aNode=0;$aNode<=$this->nodeCount;$aNode++){
      if($this->expandAll == 1 && !$e){
        $visible[$aNode] = 1;
        $expand[$aNode] = 1;
      }
      if($this->node[$aNode]["treeDepth"]==0){
        $visible[$aNode] = 1;
      }
      if($this->node[$aNode+1]["treeDepth"] > $this->node[$aNode]["treeDepth"]){
        $expandable[$aNode]=1;
      }
      else{
        if($this->expandAll == 1 && !$e){
          if($expandable[$aNode]== 1){
            $expand[$aNode] = 1;
          }
        }
      }
    }

    // Now make everything visible that is expanded + 1 level
    for ($i=0; $i<=$totalExpands; $i++)  {
      $aNode=$expands[$i];

      if ($aNode<$this->nodeCount && $visible[$aNode]==1 && $expand[$aNode]==1) {
        $nextNode=$aNode+1;
        while ( $this->node[$nextNode]["treeDepth"] > $this->node[$aNode]["treeDepth"] ) {
          if($this->node[$nextNode]["treeDepth"]== $this->node[$aNode]["treeDepth"]+1){
            $visible[$nextNode] = 1;
            $lastnode = $nextNode;
          }
          $nextNode++;
        }
        $lastNode[$lastnode] = 1;
      }
    }
    $lastlevel = $this->maxLevel;
    for ($i=$this->nodeCount-1; $i>=0; $i--) {
      if ( $this->node[$i]["treeDepth"]< $lastlevel) {
        for ($nextNode=$this->node[$i]["treeDepth"]+1; $nextNode < $this->maxLevel; $nextNode++) {
          $treeLevels[$nextNode]=0;
        }
      }
      if ( $treeLevels[$this->node[$i]["treeDepth"]]==0 ) {
        $treeLevels[$this->node[$i]["treeDepth"]]=1;
        $lastNode[$i]=1;
      }
      $lastlevel=$this->node[$i]["treeDepth"];
    }

    $treeHTML = "";  //初始化
    $treeHTML .= "<table cellspacing=0 cellpadding=0 border=0  cols=" .
    ($this->maxLevel+3) . " width=" . ($this->maxLevel*16+$width2) . ">\n";
    $treeHTML .= "<tr>";
    for ($i=0; $i<$this->maxLevel; $i++)   $treeHTML .= "<td width=16></td>";
      $treeHTML .="<td></td></tr>\n";
      for ($i=0; $i<$this->maxLevel; $i++) $treeLevels[$i]=1;
        for($aNode=0;$aNode<$this->nodeCount+1;$aNode++){
          $nextNode = $aNode+1;
          if($visible[$aNode]==1){
            $treeHTML .= "<tr>\n";
            for ($i=0; $i<$this->node[$aNode]["treeDepth"]-1; $i++) {
              if ($treeLevels[$i]==1) {
                $treeHTML .= "<td><img src=\"". $this->imgPath . $this->imgType["mbar"]. "\" alt=\"\"></td>";
              }
              else {
                $treeHTML .= "<td><img src=\"" .$this->imgPath . $this->imgType["mspace"] . "\" alt=\" \"></td>";
              }
            }
            if ($this->node[$aNode]["treeDepth"]) {
              if ($lastNode[$aNode]) {

                //--- by sogo ---
                $treeDepth1=$this->node[$aNode]["treeDepth"];

                if($aNode==$this->nodeCount-1 && $treeDepth1==$this->node[$this->nodeCount]["treeDepth"])
                  $Img = $this->imgType["mtee"] ;
                else
                  $Img = $this->imgType["mcorner"] ;


                $treeHTML .= "<td><img src=\"" . $this->imgPath . $Img . "\" alt=\"\"></td>";
                $treeLevels[$this->node[$aNode]["treeDepth"]-1]=0;
              }
            else {
              if($this->expandAll == 1){
                if($this->nodeCount  ==  $aNode ){
                  $Img2 = $this->imgType["mcorner"] ;
                }
              else {
                $Img2 = $this->imgType["mtee"] ;
              }
            }
         else{
           $Img2 = $this->imgType["mtee"] ;
         }
         $treeHTML .= "<td><img src=\"" . $this->imgPath . $Img2 . "\" alt=\"\"></td>";
         $treeLevels[$this->node[$aNode]["treeDepth"]-1]=1;
        }
      }
      if ($this->node[$nextNode]["treeDepth"]>$this->node[$aNode]["treeDepth"]) {
        $postString="?e=";
        for ($i=0; $i<$this->nodeCount; $i++) {
          if ($expand[$i]==1 xor $aNode==$i) {
            if ($postString != "?e=") $postString .= ",";
              $postString .= $i;
            }
          }
          
          if($this->PARA_ID!="")
             $postString .="/".$this->PARA_ID."/".$this->PARA_VALUE;

          if ($expand[$aNode]==0) {

            if($lastNode[$aNode]){
              $treeHTML .= "<td><a href=\"" . $PHP_SELF . $postString ."\"><img src=\"" . $this->imgPath . $this->imgType["cplus"] . "\" border=\"0\" alt=\"չ��\"></a></td>\n";
            }
            else{
              $treeHTML .= "<td><a href=\"" . $PHP_SELF . $postString ."\"><img src=\"" . $this->imgPath . $this->imgType["tplus"] . "\" border=\"0\" alt=\"չ��\"></a></td>\n";
            }
          }
          else {
            $treeHTML .= "<td><a href=\"" . $PHP_SELF  . $postString ."\"><img src=\"" . $this->imgPath . $this->imgType["tminus"] . "\" border=\"0\" alt=\"����\"></a></td>\n";
          }
        }
      else {
        if($this->node[$aNode]["TYPE"]){
          if($this->imgPath . $this->imgType[$this->node[$aNode]["TYPE"]]){
            $leafImg = $this->imgPath . $this->imgType[$this->node[$aNode]["TYPE"]];
          }
          else{
            $leafImg = $this->imgPath . $this->imgType["leaf"];
          }
        }
        if($leafImg ){
           $treeHTML .= "<td><img src=\"" . $leafImg . "\" alt=\"\"></td>\n";
        }
        else{
           $treeHTML .= "<td></td>";
        }
      }
      if ($this->node[$aNode]["SUB"] =="0" OR $aNode==0) {
         $treeHTML .= "<td  valign=top colspan=" . ($this->maxLevel-$this->node[$aNode]["treeDepth"]+3) ." nowrap >";
         //-- by sogo --
         $treeHTML .= "<img src=\"".$this->ROOT_IMAGE."\"><span class=small>&nbsp;";
         $treeHTML .=  $this->node[$aNode]["NAME"] . "<br></span>";
         $treeHTML .= "</td>\n";
      }
      else {
        $treeHTML .= "<td colspan=" . ($this->maxLevel-$this->node[$aNode]["treeDepth"]+3) ." nowrap>";
        $treeHTML .= "<span class=small>";

        //-- by sogo --
        if ($this->node[$nextNode]["treeDepth"]>$this->node[$aNode]["treeDepth"])
        {
        
          if($this->PARA_ID=="FILE_SORT")
          {
              if($expand[$aNode]==0)

                 $treeHTML .= "<img src=\"/images/menu/tree_dir.gif\">";
              else
                 $treeHTML .= "<img src=\"/images/menu/tree_diropen.gif\">";
          }
          else
              $treeHTML .= "<img src=\"". $this->imgPath . $this->imgType[$this->node[$aNode]["TYPE"]]."\">";
        }

        $treeHTML .= "<a href=\"" . $this->node[$aNode]["LINK"];
        if($aNode==0){
           $treeHTML .= "?e=0";
        }
        else{
        if($this->node[$aNode]["LINK"] == ""){
            $treeHTML .= "?e=" . $e;
        }

        if($this->PARA_ID!="")
           $treeHTML .="/".$this->PARA_ID."/".$this->PARA_VALUE;

     }
     $treeHTML .= "\" ";
     if($this->node[$aNode]["TARGET"]){
       $treeHTML .= "target=\"" . $this->node[$aNode]["TARGET"] . "\"";
     }
     $treeHTML .= ">" . $this->node[$aNode]["NAME"] . "</span></a></td>\n";
    }
   $treeHTML .= "</tr>\n";
   }
  }
  $treeHTML .= "</table></span>\n";
  Return $treeHTML;
 } // end displayTree()


} // end class



?>
