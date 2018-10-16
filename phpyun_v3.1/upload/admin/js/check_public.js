$(document).ready(function(){
	$(".job1").change(function(){
		var province=$(this).val();
		var lid=$(this).attr("lid");
		if(province==""){
			$("#"+lid+" option").remove()
			$("<option value=''>请选择</option>").appendTo("#"+lid);
			lid2=$("#"+lid).attr("lid");
			if(lid2){
				$("#"+lid2+" option").remove();
				$("<option value=''>请选择</option>").appendTo("#"+lid2);
				//$("#"+lid2).hide();
			}
		}
		$.post("../index.php?m=ajax&c=ajax_job", {"str":province},function(data) {
			if(lid!="" && data!=""){
				$('#'+lid+' option').remove();
				$(data).appendTo("#"+lid);
				job_type(lid);
			}
		})
	})
	$(".jobone").change(function(){
		var jobid=$(this).val();
		$.post("../index.php?m=ajax&c=ajax_ltjob", {"str":jobid},function(data) {
			$("#jobtwo").html(data);
		})
	})
	$(".province").change(function(){
		var province=$(this).val();
		var lid=$(this).attr("lid");
		if(province==""){
			$("#"+lid+" option").remove()
			$("<option value='0'>请选择城市</option>").appendTo("#"+lid);
			lid2=$("#"+lid).attr("lid");
			if(lid2){
				$("#"+lid2+" option").remove();
				$("<option value='0'>请选择城市</option>").appendTo("#"+lid2);
				$("#"+lid2).hide();
			}
		}
		$.post("../index.php?m=ajax&c=ajax", {"str":province},function(data) {
			if(lid!="" && data!=""){
				$('#'+lid+' option').remove();
				$(data).appendTo("#"+lid);
				city_type(lid);
			}
		})
	})
})
function job_type(id){
	var id;
	var province=$("#"+id).val();
	var lid=$("#"+id).attr("lid");
	$.post("../index.php?m=ajax&c=ajax_job", {"str":province},function(data) {
		if(lid!="" && data!=""){
			$('#'+lid+' option').remove();
			$(data).appendTo("#"+lid);
		}
	})
}
function city_type(id){
	var id;
	var province=$("#"+id).val();
	var lid=$("#"+id).attr("lid");
	$.post("../index.php?m=ajax&c=ajax", {"str[]":[province]},function(data) {
		if(lid!=""){
			if(lid!="three_cityid" && lid!="three_city" && data!=""){
				$('#'+lid+' option').remove();
				$(data).appendTo("#"+lid);
			}else{
				if(data!=""){
					$('#'+lid+' option').remove();
					$(data).appendTo("#"+lid);
					$('#'+lid).show();
				}else{
					$('#'+lid+' option').remove();
					$("<option value='0'>请选择城市</option").appendTo("#"+lid);
					$('#'+lid).hide();
				}
			}
		}
	})
}
function toDate(str){
	var sd=str.split("-");
	return new Date(sd[0],sd[1],sd[2]);
}
function check_form_job(){
	var start = $("#sdate").val();
	var end = $("#edate").val();
	var name = $.trim($("#name").val());
	if(name==''){
		parent.layer.msg('职位名称不能为空！',2,2);return false;
	}
	if($("#job1_son").val()==''){
		parent.layer.msg('请选择职位类别！',2,2);return false;
	}

	if($("#cityid").val()==''){
		parent.layer.msg('请选择工作地点！',2,2);return false;
	}
	if(start==""||end==""){
		parent.layer.msg('开始时间、结束时间不能为空！', 2, 2);return false;
	}else{
		var st=toDate(start);
		var ed=toDate(end);
		if(st>ed){
			parent.layer.msg('开始时间不得大于结束时间！', 2, 3);return false;
		}
	}
	var content = editor.text(); 
	if(content==""){
		parent.layer.msg('职位描述不能为空！',2,2);return false;
	}else{
		$("#content").val(content);
	}
}