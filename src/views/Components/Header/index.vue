<template>
  <div class="index-head">
    <div class="top-bar">
      <div class="w1200">
        <if condition="$login_name">
          <span class="s1">HI,{$login_name}!</span>
          <span class="s2"><a href="{:U('User/mchpwd')}">修改密码</a></span>
          <span class="s3"><a href="{:U('User/mlogout')}">登出</a></span>
          <else />
          <span class="s1">HI,最近想去哪里玩?</span>
          <!--<span class="s2"><a href="{:U('User/mlogin')}">业务员入口</a></span>-->
        </if>
        <!--
        <span class="s3">注册</span>
        -->
        <span class="s4">关注三三</span>
        <span class="s5" style="display: none"><img src="@images/erweima.jpg" /></span>
      </div>
    </div>
    <div class="seach-bar" style="background: #fff">
      <div class="w1200">
        <div class="logo"
          ><a href="{:U('Index/index')}"><img src="@images/logo.png" /></a
        ></div>
        <div class="search">
          <form method="get" action="{:U('Tour/search')}" class="search-form" target="_blank">
            <div class="input-text">
              <input type="text" name="keyword" class="new-text" placeholder="{$defaultKeyword}" />
            </div>
            <div class="input-submit">
              <input type="submit" class="new-button" value="" />
            </div>
          </form>
        </div>
        <div class="phone"><img src="@images/search-tel.png" /></div>
      </div>
    </div>
    <div class="nav-bar">
      <div class="block"></div>
      <div class="bar">
        <div class="bar-content">
          <!-- <a href="__ROOT__/Index/index" <if condition="$nav eq 'Index/index'">class="active"</if>>首页</a> -->
          <a href="__ROOT__/Index/index">首页</a>
          <volist name="navList" id="vo">
            <!-- <a href="__ROOT__/{$vo.url}" <if condition="$nav eq $vo['url']">class="active"</if>>{$vo.name}</a> -->
            <a href="__ROOT__/{$vo.url}" class="active">{$vo.name}</a>
          </volist>
          <a href="#" class="last">关于三三</a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts"></script>

<style lang="scss" scoped>
  .w1200 {
    width: 1200px;
    margin: 0 auto;
    position: relative;
  }
  .index-head {
    .top-bar {
      height: 30px;
      line-height: 30px;
      background: #fafafa;
      font-size: 12px;
      .s1 {
        color: #999999;
      }
      .s2 {
        margin-left: 28px;
        color: #ed6c00;
        a {
          color: #ed6c00;
        }
      }
      .s3 {
        margin-left: 10px;
        color: #555555;
        a {
          color: #ed6c00;
        }
      }
      .s4 {
        width: 90px;
        position: absolute;
        right: 0;
        top: 0;
        color: #0081cc;
        font-weight: 700;
        text-align: center;
        border-left: 1px solid #fafafa;
        border-right: 1px solid #fafafa;
        &.active {
          background: #fff;
          border-left: 1px solid #ccc;
          border-right: 1px solid #ccc;
        }
      }
      .s5 {
        width: 90px;
        height: 90px;
        position: absolute;
        right: 0;
        top: 30px;
        z-index: 2;
        border-left: 1px solid #ccc;
        border-right: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
        img {
          vertical-align: top;
        }
      }
    }
  }
  .seach-bar{
  	height: 90px;
    .logo{
      position: absolute;
      top: 20px;
      left: 0;
    }
    .search{
      position: absolute;
      left: 333px;
      top: 30px;
    }
    .phone{
      position: absolute;
      right: 0;
      top: 30px;
    }
  }
  .search-form{
	width: 550px;
	height: 30px;
	border: 1px solid #0081cc;
	position: relative;
}
.search-form .new-text{
	width: 460px;
	height: 30px;
	line-height: 30px;
	padding: 0 15px;
	font-size: 13px;
	color: #999999;
}
.search-form .input-submit{
	width: 60px;
    height: 30px;
    margin: 0 auto;
    display: block;
    background: url('@images/search-btn.png') no-repeat;
	position: absolute;
	right: 0;
	top: 0;
	cursor: pointer;
}
.search-form .new-button{
	cursor: pointer;
    width: 60px;
    height: 30px;
    background: none;
}
</style>
