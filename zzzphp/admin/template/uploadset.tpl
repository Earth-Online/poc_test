<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">

<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>上传参数设置</title>
<link href="../plugins/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/font-awesome.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/animate.min.css" rel="stylesheet">
<link href="../plugins/bootstrap/style.min.css" rel="stylesheet">
<link href="css/adminstyle.css" rel="stylesheet">
<script src="../js/jquery.min.js"></script>
<script src="../plugins/colorpicker/bootstrap-colorpicker.min.js"></script>
<!--[if lte IE 9]>
<script src="../js/respond.min.js"></script>
<script src="../js/html5.js"></script>
<![endif]-->
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">
  <div class="ibox float-e-margins">
    <div class="row">
      <form method="post" action="save.php?act=saveupload" class="form-horizontal" id="contentform"> <div class="col-sm-12">
          <div class="tabs-container">
            <ul class="nav nav-tabs">
              <li class="tab1 active"><a data-toggle="tab" href="#tab-1" > <i class="fa fa-upload"></i>上传功能</a> </li>
              <li class="tab2"><a data-toggle="tab" href="#tab-2"><i class="fa fa-file-image-o"></i> 图片上传</a> </li>
              <li class="tab3"><a data-toggle="tab" href="#tab-3"><i class="fa fa-file-word-o"></i> 附件上传</a> </li>
              <li class="tab4"><a data-toggle="tab" href="#tab-4"><i class="fa fa-file-video-o"></i> 视频上传</a> </li>
              <li class="tab5"><a data-toggle="tab" href="#tab-5"><i class="fa fa-image"></i> 缩略图</a> </li>
            </ul>
            <div class="tab-content">
              <div id="tab-1" class="tab-pane active">
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-sm-1 control-label text-danger">上传开关</label>
                    <div class="col-sm-1" id="uploadmark">
                      <input type="checkbox"  name="uploadmark" class="js-switch" value="1" {$check_onoff [c uploadmark],"checked"}>
                      <span class="help-block m-b-none">{$check_onoff [c uploadmark],"ch"}</span> </div>
                    <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 关闭后，所有上传功能失效。</span> </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label text-danger">上传目录</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:uploadpath]" required name="uploadpath" id="uploadpath" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label text-danger">子目录</label>
                    <div class="col-sm-2">
                      <select name="datefolder" class="form-control" onclick="layer.tips('图片较多推荐创建',this)">
                        <option value="0" {$check_onoff [c datefolder],"selected"}>不创建</option>
                        <option value="1" {$check_onoff [c datefolder],"selected"}>创建日期子文件夹</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label text-danger">重复</label>
                    <div class="col-sm-2">
                      <select name="covermark" class="form-control"  onclick="layer.tips('推荐使用替换',this)">
                        <option value="0" {$check_onoff [c covermark],"selected",'',0}>尾部加数字</option>
                        <option value="1" {$check_onoff [c covermark],"selected",'',1}>替换</option>
                        <option value="2" {$check_onoff [c covermark],"selected",'',2}>略过</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div id="tab-2" class="tab-pane">
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-sm-1 control-label">图片类型</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:imageext]" required name="imageext" id="imageext" class="form-control"  onfocus="layer.tips('上传图片允许格式',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">最大</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:imagemaxsize]" required name="imagemaxsize" id="imagemaxsize" class="form-control" onfocus="layer.tips('请一定要小于空间限制',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">命名</label>
                    <div class="col-sm-2">
                      <select name="imageformat" class="form-control">
                        <option value="shijian"		{$check_on [c imageformat],"shijian","selected"}>时间</option>
                        <option value="pinyin"		{$check_on [c imageformat],"pinyin","selected"}>拼音</option>
                        <option value="yuanming"	{$check_on [c imageformat],"yuanming","selected"}>原名</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">压缩宽度</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:compresswidth]" required name="compresswidth" id="compresswidth" class="form-control"  onfocus="layer.tips('超过此宽度缩放',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">压缩高度</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:compressheight]" required name="compressheight" id="compressheight" class="form-control" onfocus="layer.tips('超过此高度缩放',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">压缩质量</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:compressquality]" required name="compressquality" id="compressquality" class="form-control" onfocus="layer.tips('压缩质量1-100',this)">
                    </div>
                  </div>
                </div>
              </div>
              <div id="tab-3" class="tab-pane">
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-sm-1 control-label">附件类型</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:fileext]" required name="fileext" id="fileext" class="form-control"  onfocus="layer.tips('上传附件允许格式',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">最大</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:filemaxsize]" required name="filemaxsize" id="filemaxsize" class="form-control" onfocus="layer.tips('请一定要小于空间限制',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">命名</label>
                    <div class="col-sm-2">
                      <select name="fileformat" class="form-control">
                         <option value="shijian"	{$check_on [c fileformat],"shijian","selected"}>时间</option>
                        <option value="pinyin"		{$check_on [c fileformat],"pinyin","selected"}>拼音</option>
                        <option value="yuanming"	{$check_on [c fileformat],"yuanming","selected"}>原名</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div id="tab-4" class="tab-pane">
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-sm-1 control-label">视频类型</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:videoext]" required name="videoext" id="videoext" class="form-control" onfocus="layer.tips('上传视频允许格式',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">最大</label>
                    <div class="col-sm-2">
                      <input type="text" value="[c:videomaxsize]" required name="videomaxsize" id="videomaxsize" class="form-control" onfocus="layer.tips('请一定要小于空间限制',this)">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="col-sm-1 control-label">命名</label>
                    <div class="col-sm-2">
                      <select name="videoformat" class="form-control">                        
                          <option value="shijian"	{$check_on [c videoformat],"shijian","selected"}>时间</option>
                        <option value="pinyin" 		{$check_on [c videoformat],"pinyin","selected"}>拼音</option>
                        <option value="yuanming"	{$check_on [c videoformat],"yuanming","selected"}>原名</option>
                      </select>
                    </div>
                  </div>
                </div>
              </div>
              <div id="tab-5" class="tab-pane">
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-sm-1 control-label">微缩图开关</label>
                    <div class="col-sm-1" id="smallmark">
                      <input type="checkbox"  name="smallmark" class="js-switch" value="1" <?php echo check_onoff($conf['smallmark'],"checked")?> >
                      <span class="help-block m-b-none"><?php echo check_onoff($conf['smallmark'],"ch")?></span> </div>
                  </div>
                  <div class="show-<?php echo $conf['smallmark']?>"  id="showsmall">     
                   <div class="form-group">
                      <label class="col-sm-1 control-label">缩略图设置</label>
                      <div class="col-sm-2">
                        <select name="smallmodel" id="smallmodel" class="form-control">
                      <option>选择模型</option>
                       {$select_model '',array('about','brand','content')}
                        </select>
                      </div>
                    </div>
                    {loop array('about','brand','product','news','job','down','case','video','photo') $val}                   
                    <div id="{$val}small" class="smallpic" style="display:none">
                    <div class="form-group">
                    <label class="col-sm-1 control-label">裁切方式</label>
                    <div class="col-sm-2">
                    <select name="{$val}_mode" class="form-control">
                      <option value="0" <?php echo check_onoff($conf[$val.'_mode'],"selected",'',0)?>>取最大值</option>
                      <option value="1" <?php echo check_onoff($conf[$val.'_mode'],"selected",'',1)?>>取最小值</option>
                      <option value="2"  <?php echo check_onoff($conf[$val.'_mode'],"selected",'',2)?>>最大宽</option>
                      <option value="3"  <?php echo check_onoff($conf[$val.'_mode'],"selected",'',3)?>>最大高</option>                      
                      <option value="4"  <?php echo check_onoff($conf[$val.'_mode'],"selected",'',4)?>>左上宽×高</option>
                      <option value="5"  <?php echo check_onoff($conf[$val.'_mode'],"selected",'',5)?>>居中宽×高</option>
                    </select>
                  </div> </div>
                    <div class="form-group" >
                      <label class="col-sm-1 control-label">缩略图宽</label>
                      <div class="col-sm-2">
                        <input type="text" value="<?php echo $conf[$val.'_width']?>" required name="{$val}_width" id="{$val}_width"  class="form-control"  onfocus="layer.tips('缩略图宽（像素）',this);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-1 control-label">缩略图高</label>
                      <div class="col-sm-2">
                        <input type="text" value="<?php echo $conf[$val.'_height']?>" required name="{$val}_height" id="{$val}_height" class="form-control"  onfocus="layer.tips('缩略图高（像素）',this);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-1 control-label">质量度</label>
                      <div class="col-sm-2">
                        <input type="number" value="<?php echo $conf[$val.'_quality']?>" required name="{$val}_quality" id="{$val}_quality"  class="form-control"  onfocus="layer.tips('影响缩略图大小',this);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-1 control-label">更新模型</label>
                      <div class="col-sm-2">
                        <button onclick="smallpic('{$val}')" class="btn btn-info" type="button"><i class="fa fa-refresh"></i> 重新生成缩略图</button>
                      </div>
                    </div>
                   </div>
                   {/loop}                  
                    <div class="form-group">
                      <label class="col-sm-1 control-label">全部更新</label>
                      <div class="col-sm-2">
                    <button onclick="smallpic('all')" class="btn btn-primary" type="button"><i class="fa fa-refresh"></i> 更新全部缩略图</button>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-12  m-t">
          <div class=" col-sm-10 col-md-offset-1">
            <button class="btn btn-primary" type="submit"><i class="fa fa-floppy-o"></i>　保存内容</button>
          </div>
        </div>
      </form>
      
      <!-- End Panel Other --> 
    </div>
  </div>
</div>
<script src="../plugins/bootstrap/bootstrap.min.js"></script>
<link href="../plugins/colorpicker/bootstrap-colorpicker.min.css" rel="stylesheet">
<script src="../plugins/switchery/switchery.js"></script>
<link href="../plugins/switchery/switchery.css" rel="stylesheet">
<script src="../plugins/layer/layer.min.js"></script> 
<script src="js/adminjs.js"></script> 
<script>
$(function () {
var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
	elems.forEach(function(html) {
  var switchery = new Switchery(html);
   	html.onchange = function() {
	var help=$(this).parent().find(".help-block")
	if(html.checked==true) {help.text("已开启")}
	else{help.text("已关闭")}
	};
});

  $("#smallmodel").change(function(){
	 var val=$(this).val();
	 $(".smallpic").hide();
	  $("#"+val+"small").show();
  })
  $("#smallmark").click(function () {
	  if ($("#smallmark input").is(":checked")) {
		  $("#showsmall").css("display", "block");
	  }
	  else {
		  $("#showsmall").css("display", "none");
	  }
  });
  $("#makesmall").click(function () {
	   var model=$("#smallmodel").val();
	   alert(model);
  }); 
    if(location.hash) {
        $('a[href=' + location.hash + ']').tab('show');
    }
    $(document.body).on("click", "a[data-toggle]", function(event) {
        location.hash = this.getAttribute("href");
    });
});
$(window).on('popstate', function() {
    var anchor = location.hash || $("a[data-toggle=tab]").first().attr("href");
    $('a[href=' + anchor + ']').tab('show');
});
</script>
</body>
</html>