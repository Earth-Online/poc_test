var $table = $("#table"), $remove = $('#remove'),$move = $('#move'),$copy = $('#copy'),$del = $('#del'),$recovery=$('#recovery'),ids='';	
$('li.dropdown').mouseover(function() {$(this).addClass('open');}).mouseout(function() {$(this).removeClass('open');}); 
//$('li.leftdown').mouseover(function() {$(this).children("ul").show();}).mouseout(function() {$(this).children("ul").hide();}); 
function closelayer(){parent.layer.closeAll()}
function closetab(){parent.$(".J_tabClosethis").trigger("click"); }
function opennew(url){layer.open({type:2,shade:0.8, maxmin: true, area:['900px','95%'],content:url});}
function openonoff(url){layer.open({ type: 2,title: false,shadeClose:true,shade: 0.8,area: ['180px', '65px'], content:url,end:function(){location.reload();}});}
function openimg(str){parent.layer.open({type:1,shadeClose:true,shade:0.8,content:'<img src='+str+'>'});}
function opendiv(str){parent.layer.open({type:1,shadeClose:true,shade:0.8,content:str});}
function remove(id,table){$.post("save.php?act=remove",{"id":id,'table':table},function(data) {$table.bootstrapTable('refresh');});}
function recovery(id,table){$.post("save.php?act=recovery",{'id':id,'table':table}, function(data){$table.bootstrapTable('refresh');});}
function delid(id,table){layer.confirm('确定删除吗？',function(index){layer.close(index);$.post("save.php?act=delid",{"id":id,'table':table},function(data){$table.bootstrapTable('refresh');});})}
function delcustom(custom){layer.confirm('确定删除参数吗，对应参数内容将被同时删除？',function(index){layer.close(index);$.post("save.php?act=delcustom",{"custom":custom},function(data){location.reload();});})}
function moveid(){cid=$('#moveid').val();col=$("#moveSortID").val();if(cid.length==0||col.length==0){layer.alert('请选择内容及分类！');return false;}
$.post("save.php?act=moveid",{'id':cid,'col':col},function(data){$(".close").click();layer.open({title:'保存成功',content:'移动成功',icon:4,time:3000,end:function(){location.reload();}});})};
function copyid(){cid=$('#copyid').val();sid=$("#copySortID").val();if(cid.length==0||sid.length==0){layer.alert('请选择内容及分类！');return false;}
$.post("save.php?act=copyid",{'id':cid,'sid':sid}, function(data){$(".close").click();layer.open({title:'保存成功',content:'复制成功',icon: 4,time: 3000,end:function(){location.reload();}});})};
function smallpic(type){$.post("save.php?act=smallpic&type="+type,function(data){layer.open({title:'更新完成',content:'更新完成',icon:4,time:3000});})}
function delsort(id){layer.confirm('确定删除吗？栏目下内容会一同删除不可恢复！',function(index){layer.close(index);$.post("save.php?act=delsort",{'id':id}, function(data) {location.reload();});})}
function delall(table){layer.confirm('确定删除全部记录吗？删除后不可恢复！',function(index){layer.close(index);$.post("save.php?act=delall",{'table':table}, function(data){location.reload();});})}
function delfile(path){layer.confirm('确定删除此文件吗？', 	function(index){layer.close(index);$.post("save.php?act=delfile",{"path":path},function(data){location.reload();});});}
function delallfile(type){layer.confirm('确定删除全部吗？删除不可恢复！', function(index){layer.close(index);$.post("save.php?act=delallfile",{"type":type},function(data){location.reload();});});}
function restore(path){layer.confirm('确定恢复吗？',function(index){layer.close(index);var index=layer.load();$.post("save.php?act=restore",{'path':path},function(data){location.reload();});})}
function backup(){$.post("save.php?act=backup", function(data){ $table.bootstrapTable('refresh');});}
function setcol(col,colval,id){$.post("save.php?act=setcol",{"table":table,"col":col,"colval":colval,"id":id}, function(data) {$table.bootstrapTable('refresh');});}
function UpperFirstLetter(str){str=str.replace(/[ ]/g,"");return str.substring(0,1).toUpperCase()+str.substring(1);}
function setjeDate(elem,id){$.jeDate(elem,{isinitVal:true});}
function goparent(id){parent.$("#"+id+" a").click()}
$(document).keydown(function(e){if(e.ctrlKey&&e.keyCode==13){if($("#contentform").length>0){$("#contentform").submit()}}
var keyEvent=false;switch(e.keyCode){case 27:{if($("#contentform").length>0)
{layer.confirm('确定关闭吗？',function(){parent.layer.closeAll()})}
else
{layer.confirm('确定关闭吗？',function(){$(parent.parent.document.body).find(".J_tabClosethis").click();layer.closeAll();parent.layer.closeAll()})}}
case 8:{var d=e.srcElement||e.target;if(d.tagName.toUpperCase()=='INPUT'||d.tagName.toUpperCase()=='TEXTAREA'){keyEvent=d.readOnly||d.disabled;}else{keyEvent=true;}}}
if(keyEvent){e.preventDefault();}});
$("#search_plug").change(function(){		
		var val=$(this).val()
		$("#list li .title").each(function() {
           text=$(this).text()
		  if(text.indexOf(val)>=0){
			  $(this).parent().parent("li").show()
			}else
			{
			$(this).parent().parent("li").hide()	
			}	  
        });
	})
	
$table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function () {
	  $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
	  $move.prop('disabled', !$table.bootstrapTable('getSelections').length);
	  $recovery.prop('disabled', !$table.bootstrapTable('getSelections').length);
	  $copy.prop('disabled', !$table.bootstrapTable('getSelections').length);	  
	  $del.prop('disabled', !$table.bootstrapTable('getSelections').length);	  
	  ids = $.map($table.bootstrapTable('getSelections'), function (row) {
		  return row.id
	  });
  });
      	$move.click(function () {		
		$("#moveid").val(ids)      
			if ($("#moveSortID option").length ==1) {
             $.post("function.php?act=selectsort",{'type':stype}, function(data){
				 $("#moveSortID").append(data);				
			 }); 
			}
        });

		$copy.click(function () {         
          $("#copyid").val(ids)
			if ($("#copySortID option").length ==1) {
             $.post("function.php?act=selectsort",{'type':stype}, function(data){
				 $("#copySortID").append(data); 				
			 }); 
			}  
        });
		$recovery.click(function () {     
			$.post("save.php?act=recovery",{'table':table,'id':ids}, function(data){
			  $table.bootstrapTable('refresh');
		    });
        });
		$del.click(function () {     
		  layer.confirm('确定删除吗？', function(index){   
		  	layer.close(index);
            $.post("save.php?act=delid",{'table':table,'id':ids}, function(data){
			  $table.bootstrapTable('refresh');
		    });
		  });
        });
	 	$remove.click(function () {    
		  layer.confirm('删除到回收站？', function(index){   
		  	layer.close(index);     
         	 $.post("save.php?act=remove",{'table':table,'id':ids}, function(data){
				$table.bootstrapTable('refresh');
			});
		  });
        });
$table.on("blur", "input", function() {
	var type = $(this).attr("type");
	if (type=="text"||type=="number"){
    var colval = $(this).val();		
    var colplace = $(this).attr('placeholder')
    var colid = $(this).attr('id').split("-")[1];
    var colname = $(this).attr('id').split("-")[0];
    if (colval != colplace) {
        $(this).attr('placeholder', colval);
        saveid(table,colid,colname,colval)
    }}
});
function saveid(table,colid,colname,colval){
 	$.post("save.php?act=saveid",{"table":table,"colval":colval,"colid":colid,"colname":colname},function(data){layer.msg('保存成功');});
}
function savehtmlpath(type){$.post("save.php?act=saveid",{"table":'language',"colname":type,"colval":$("#"+type).val(),"colid":1},function(data){layer.msg('保存成功');});}