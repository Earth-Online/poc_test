
function checkjob(id,type){
	if(id>0){
		$.post(weburl+"/index.php?m=ajax&c=wap_job",{id:id,type:type},function(data){
			if(type==1){
				$("#job1_son").html(data);
			}else{
				$("#job_post").html(data);
			}
		})
	}else{
		if(type==1){
			$("#job1_son").html('<option value="">请选择</option>');
		}
		$("#job_post").html('<option value="">请选择</option>');
	}
}
function checkcity(id,type){
	if(id>0){
		$.post(weburl+"/index.php?m=ajax&c=wap_city",{id:id,type:type},function(data){
			if(type==1){
				$("#cityid").html(data);
			}else{
				$("#three_cityid").html(data);
			}
		})
	}else{
		if(type==1){
			$("#cityid").html('<option value="">请选择</option>');
		}
		$("#three_cityid").html('<option value="">请选择</option>');
	}
}
function checkfrom(){
	if($.trim($("#name").val())==""){
		alert("招聘名称不能为空！");
		return false;
	}
	if($.trim($("#job_post").val())==""){
		alert("请选择职位类别！");
		return false;
	}
	if($.trim($("#cityid").val())==""){
		alert("请选择工作地点！");
		return false;
	}
	if($.trim($("#days").val())<1){
		alert("请正确填写招聘天数！");
		return false;
	}
	if($.trim($("#description").val())==""){
		alert("职位描述不能为空！");
		return false;
	}
}