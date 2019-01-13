<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>找回密码-{zzz:sitetitle}</title>
<meta name="description" content="{zzz:sietdesc}"/>
<meta name="Keywords" content="{zzz:sitekeys}"/>
<meta name="author" content="http://www.zzzcms.com"/>
<link rel="stylesheet" type="text/css" href="{zzz:tempath}Css/styles.css"/>
<script src="{zzz:sitepath}js/jquery.min.js" type="text/javascript"></script>
<script src="{zzz:plugpath}layer/layer.min.js" type="text/javascript"></script>
<script src="{zzz:sitepath}js/zzzcms.js" type="text/javascript"></script>
<link href="{zzz:sitepath}plugins/Validform/Validform.css" rel="stylesheet" />
<link href="{zzz:plugpath}template/user.css" rel="stylesheet" />
<script src="{zzz:sitepath}plugins/Validform/Validform.min.js"></script>
<script type="text/javascript">
    $(function() {
        $(".registerform").Validform({
            tiptype: function(msg, o, cssctl) {
                if (!o.obj.is("form")) {
                    var objtip = o.obj.siblings(".Validform_checktip");
                    cssctl(objtip, o.type);
                    objtip.text(msg);
                }
            }
        });
    })
</script>
</head>

{zzz:top}
<div class="in_newsbox">
  <div class="register">
    <div class="leftregister">
      <div class="registerbutton">
        <h3>已有账号？</h3>
       <a href="{zzz:sitepath}?location=user&act=login&type=[user:type]&backurl=[user:backurl]" class="nowreg ">立即登陆</a>
        <h3>没有账号？</h3>
        <a href="{zzz:sitepath}?location=user&act=reg&type=[user:type]&backurl=[user:backurl]">注册账号</a> </div>
    </div>
    <div class="rightregister">
      <h2>问题登陆</h2>
      <form class="registerform" method="post" action="{zzz:sitepath}form/?forget">
      <input type="hidden" name="type" value="[user:type]">
      <input type="hidden" name="backurl" value="[user:backurl]">
        <div class="formsub">
          <ul>
            <li>
              <label><span class="need">*</span> 手机：</label>
              <input type="text" value="" name="mobile" class="inputxt" id="phonenum" datatype="*5-18" nullmsg="请输入手机号" ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkmobile&checktype=1" errormsg="账号范围5~18" />
              <div class="Validform_checktip">找回密码需填写手机号</div>
            </li>
            {if:{conf:smsmark}=0 || {conf:forgetsendsms}=0} 
            <li>
              <label><span class="need">*</span> 问题：</label>
              <input type="text" value="" name="question" class="inputxt" datatype="*" errormsg="不能为空" />
              <div class="Validform_checktip">密码问题</div>
            </li>
            <li>
              <label><span class="need">*</span> 答案：</label>
              <input type="text" value="" name="answer" class="inputxt" datatype="*" errormsg="不能为空" />
              <div class="Validform_checktip">密码答案</div>
            </li>
            {else}
            <script src="{zzz:sitepath}plugins/sms/sms.js" type="text/javascript"></script>
            <li>
              <label><span class="need">*</span>手机验证：</label>
              <input type="text" value="" name="smscode" id="smscode" class="inputxt" datatype="n4-4" ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkcode" nullmsg="请输入短信验证码" errormsg="验证码错误">
              <input id="sedcond" value="获取验证码" type="button">
              <div class="Validform_checktip">单击获取短信验证码</div>
            </li>
            {end if}        
            {if:{conf:usercode}=1}
            <li>
              <label><span class="need">*</span> 验证码：</label>
              <input type="text" value="" name="code" id="imgcode" class="inputxt" ajaxurl="{zzz:sitepath}plugins/sms/sms.php?act=checkcode" datatype="*4-4"  nullmsg="请输入验证码" errormsg="验证码错误" />
              <div class="imgcode"> <img src="{zzz:sitepath}inc/imgcode.php" id="SeedCode" align="absmiddle" style="cursor:pointer;" border="0"/> </div>
              <div class="Validform_checktip">请输入验证码</div>
            </li>
            {end if}
          </ul>
          <div class="action">
            <input type="submit" value="提交" />
            <input type="reset" value="重置" />
          </div>
        </div>
      </form>
    </div>
    <div class="clear"></div>
  </div>
  <div class="clear"></div>
</div>
{zzz:foot}
</body></html>
