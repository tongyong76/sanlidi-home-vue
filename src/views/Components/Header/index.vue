<template>
  <div class="header-page">
    <!--出发城市登陆注册开始-->
    <div class="topBar">
      <div class="w1200">
        <!--出发城市开始-->
        <div class="starting">
          <span class="top_hello fl">苏州青旅三三旅游欢迎您！您可选择出发城市：</span>
          <a class="s_city_name" href="http://www.33ly.com" title="苏州旅行社">苏州</a>
          <a class="s_city_name" href="http://kunshan.33ly.com" title="昆山旅行社" target="_blank"
            >昆山</a
          >
          <a class="s_city_name" href="http://changshu.33ly.com" title="常熟旅行社" target="_blank"
            >常熟</a
          >
          <a
            class="s_city_name"
            href="http://zhangjiagang.33ly.com"
            title="张家港旅行社"
            target="_blank"
            >张家港</a
          >
          <a class="s_city_name" href="http://taicang.33ly.com" title="太仓旅行社" target="_blank"
            >太仓</a
          >
        </div>
        <!--出发城市结束-->
        <!--登陆注册开始-->
        <div class="login">
          <template v-if="!uid">
            <span class="shu_line">|</span
            ><a class="my_ss" href="javascript:void(0)" onclick="headlogin();" rel="nofollow"
              >登录</a
            >
            <span class="shu_line">|</span
            ><a class="my_ss" href="{:C('site_domain')}/register.html" rel="nofollow">注册</a>
          </template>
          <template v-else>
            <span class="welcome">欢迎您，{$uname}</span>
            <span class="shu_line">|</span><a class="my_ss" href="{:U('User/logout')}">注销</a>
            <!--<span class="shu_line">|</span><a class="my_ss" href="javascript:void(0)" onclick="logout();">注销</a>-->
            <span class="shu_line">|</span
            ><a class="my_ss" href="{:C('site_domain')}/member/" target="_blank" rel="nofollow"
              >我的订单</a
            >
          </template>
          <span class="shu_line">|</span
          ><a class="my_ss" href="http://www.33ly.com/aboutus/brief.html" target="_blank"
            >关于我们</a
          >
          <div class="wx fl" id="wx">
            <div
              class="w_text"
              @mousemove="handleShowWxScan(true)"
              @mouseleave="handleShowWxScan(false)"
            >
              <span class="w_title fl">三三微信</span>
              <span class="w_ico fr"></span>
            </div>
            <div class="w_scan" v-if="showWxScan">
              <!--微信二维码-->
              <img src="@images/qrcode.jpg" alt="苏州青旅三三旅游微信公众号二维码" />
            </div>
          </div>
        </div>
        <!--登陆注册结束-->
      </div>
    </div>
    <div class="bannerBar">
      <div class="inside_banner">
        <a href="#" target="_blank">
          <img src="@images/visa-cate-1.png" />
        </a>
      </div>
    </div>
    <!--搜索开始-->
    <div class="searchBar">
      <div class="w1200">
        <div class="logo">
          <a href="__ROOT__/" target="_blank">
            <img
              src="@images/logo.png"
              alt="苏州青旅三三旅游是苏州旅游直营连锁第一品牌，每年为超过10万名用户提供旅游预订服务，并致力于为大众带来及时、专业、全面、无忧的旅游服务。24小时咨询预订电话：66667777"
            />
          </a>
        </div>
        <form
          method="get"
          id="searchform"
          action="{:C('site_domain')}/search"
          class="search"
          target="_blank"
        >
          <div class="s_icon"></div>
          <input id="s_input" class="s_input hint" placeholder="千岛湖" value="" maxlength="58" />
          <button class="s_btn" type="submit">搜索</button>
        </form>
        <div class="phone">
          <img src="@images/tel.png" alt="苏州青旅三三旅游24小时咨询预订电话：66667777" />
        </div>
      </div>
    </div>
    <!--搜索结束-->
    <!--导航开始-->
    <div class="navBar">
      <div class="w1200">
        <div class="l_menus">
          <div class="top_btn">
            <img src="@images/all_product_2.png" alt="苏州青旅三三旅游品牌和产品" />
          </div>
          <ul class="menus">
            <li class="menu-item">
              <div class="front">
                <dt>子品牌</dt>
                <dd>
                  <a
                    href="{:C('site_domain')}/pinpai/zizhutuan/"
                    target="_blank"
                    title="三三设计、三三收客、三三导游、三三接送、全程管控、一管到底"
                    >自组团</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zhuanxiang/"
                    target="_blank"
                    title="特价、适合大众的旅游产品"
                    >专享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/changxiang/"
                    target="_blank"
                    title="中档、性价比高的旅游产品"
                    >畅享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zunxiang/"
                    target="_blank"
                    title="高标准的定制旅游产品"
                    >尊享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/shexiang/"
                    target="_blank"
                    title="最高标准定制的小包团"
                    >奢享</a
                  >

                  <br />
                  <a
                    href="{:C('site_domain')}/pinpai/shuaicang/"
                    target="_blank"
                    title="最全的上海出港邮轮产品"
                    >沪上邮轮</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/tuanyou/"
                    target="_blank"
                    title="一天一百元的特价活动"
                    >团游</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zhoumoditu/"
                    target="_blank"
                    title="周末出发的周边游产品"
                    >周末地图</a
                  >
                </dd>
              </div>
              <div class="backend"></div>
            </li>
            <li class="menu-item">
              <div class="front">
                <dt>周边游</dt>
                <dd>
                  <a href="{:C('site_domain')}/zhoubian/qingdao/" title="青岛旅游" target="_blank"
                    >青岛</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/jiande/" title="建德旅游" target="_blank"
                    >建德</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/ningbo/" title="宁波旅游" target="_blank"
                    >宁波</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/xiangshan/" title="象山旅游" target="_blank"
                    >象山</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/tonglu/" title="桐庐旅游" target="_blank"
                    >桐庐</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/linan/" title="临安旅游" target="_blank"
                    >临安</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/shanlingshuixiu/"
                    title="山灵水秀"
                    target="_blank"
                    >山灵水秀</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/shuixiangguzhen/"
                    title="水乡古镇"
                    target="_blank"
                    >古镇</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/dushimingcheng/"
                    title="都市名城"
                    target="_blank"
                    >名城</a
                  >
                </dd>
              </div>
              <div class="backend"></div>
            </li>
            <li class="menu-item">
              <div class="front">
                <dt>子品牌</dt>
                <dd>
                  <a
                    href="{:C('site_domain')}/pinpai/zizhutuan/"
                    target="_blank"
                    title="三三设计、三三收客、三三导游、三三接送、全程管控、一管到底"
                    >自组团</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zhuanxiang/"
                    target="_blank"
                    title="特价、适合大众的旅游产品"
                    >专享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/changxiang/"
                    target="_blank"
                    title="中档、性价比高的旅游产品"
                    >畅享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zunxiang/"
                    target="_blank"
                    title="高标准的定制旅游产品"
                    >尊享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/shexiang/"
                    target="_blank"
                    title="最高标准定制的小包团"
                    >奢享</a
                  >

                  <br />
                  <a
                    href="{:C('site_domain')}/pinpai/shuaicang/"
                    target="_blank"
                    title="最全的上海出港邮轮产品"
                    >沪上邮轮</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/tuanyou/"
                    target="_blank"
                    title="一天一百元的特价活动"
                    >团游</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zhoumoditu/"
                    target="_blank"
                    title="周末出发的周边游产品"
                    >周末地图</a
                  >
                </dd>
              </div>
              <div class="backend"></div>
            </li>
            <li class="menu-item">
              <div class="front">
                <dt>周边游</dt>
                <dd>
                  <a href="{:C('site_domain')}/zhoubian/qingdao/" title="青岛旅游" target="_blank"
                    >青岛</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/jiande/" title="建德旅游" target="_blank"
                    >建德</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/ningbo/" title="宁波旅游" target="_blank"
                    >宁波</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/xiangshan/" title="象山旅游" target="_blank"
                    >象山</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/tonglu/" title="桐庐旅游" target="_blank"
                    >桐庐</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/linan/" title="临安旅游" target="_blank"
                    >临安</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/shanlingshuixiu/"
                    title="山灵水秀"
                    target="_blank"
                    >山灵水秀</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/shuixiangguzhen/"
                    title="水乡古镇"
                    target="_blank"
                    >古镇</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/dushimingcheng/"
                    title="都市名城"
                    target="_blank"
                    >名城</a
                  >
                </dd>
              </div>
              <div class="backend"></div>
            </li>
            <li class="menu-item">
              <div class="front">
                <dt>子品牌</dt>
                <dd>
                  <a
                    href="{:C('site_domain')}/pinpai/zizhutuan/"
                    target="_blank"
                    title="三三设计、三三收客、三三导游、三三接送、全程管控、一管到底"
                    >自组团</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zhuanxiang/"
                    target="_blank"
                    title="特价、适合大众的旅游产品"
                    >专享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/changxiang/"
                    target="_blank"
                    title="中档、性价比高的旅游产品"
                    >畅享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zunxiang/"
                    target="_blank"
                    title="高标准的定制旅游产品"
                    >尊享</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/shexiang/"
                    target="_blank"
                    title="最高标准定制的小包团"
                    >奢享</a
                  >

                  <br />
                  <a
                    href="{:C('site_domain')}/pinpai/shuaicang/"
                    target="_blank"
                    title="最全的上海出港邮轮产品"
                    >沪上邮轮</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/tuanyou/"
                    target="_blank"
                    title="一天一百元的特价活动"
                    >团游</a
                  >
                  <a
                    href="{:C('site_domain')}/pinpai/zhoumoditu/"
                    target="_blank"
                    title="周末出发的周边游产品"
                    >周末地图</a
                  >
                </dd>
              </div>
              <div class="backend"></div>
            </li>
            <li class="menu-item">
              <div class="front">
                <dt>周边游</dt>
                <dd>
                  <a href="{:C('site_domain')}/zhoubian/qingdao/" title="青岛旅游" target="_blank"
                    >青岛</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/jiande/" title="建德旅游" target="_blank"
                    >建德</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/ningbo/" title="宁波旅游" target="_blank"
                    >宁波</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/xiangshan/" title="象山旅游" target="_blank"
                    >象山</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/tonglu/" title="桐庐旅游" target="_blank"
                    >桐庐</a
                  >
                  <a href="{:C('site_domain')}/zhoubian/linan/" title="临安旅游" target="_blank"
                    >临安</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/shanlingshuixiu/"
                    title="山灵水秀"
                    target="_blank"
                    >山灵水秀</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/shuixiangguzhen/"
                    title="水乡古镇"
                    target="_blank"
                    >古镇</a
                  >
                  <a
                    href="{:C('site_domain')}/zhoubian/dushimingcheng/"
                    title="都市名城"
                    target="_blank"
                    >名城</a
                  >
                </dd>
              </div>
              <div class="backend"></div>
            </li>
          </ul>
        </div>
        <div class="r_navs">
          <ul class="n_main_name clearfix">
            <li><a href="/" class="on">首页</a></li>
            <li><a href="#">出境游</a></li>
            <li><a href="#">出境游</a></li>
            <li><a href="#">出境游</a></li>
            <li><a href="#">出境游</a></li>
            <li><a href="#">出境游</a></li>
          </ul>
          <div class="n_recommend">
            <p class="team">
              <a href="#" target="_blank" class="on">定制包团</a>
            </p>
            <span class="recommend_logo"></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref } from 'vue';
  const uid = ref(1);
  const showWxScan = ref(false);

  const handleShowWxScan = (status: boolean) => {
    showWxScan.value = status;
  };
</script>

<style lang="scss" scoped>
  .w1200 {
    width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .topBar {
    background: none repeat scroll 0 0 #fafafa;
    position: relative;
    width: 100%;
    height: 29px;
    border-bottom: 1px solid #f1f1f1;
    .w1200 {
      height: 29px;
    }
    .starting {
      height: 29px;
      line-height: 29px;
      _overflow: hidden;
      .top_hello {
        display: inline-block;
      }
      .s_city_name {
        margin-right: 10px;
        display: inline-block;
      }
    }
    .login {
      height: 29px;
      line-height: 29px;
      font-size: 12px;
      display: flex;
      .shu_line {
        margin: 0px 8px;
        color: #c8c8c8;
      }
      .welcome {
        float: left;
      }
      .my_ss {
        display: inline-block;
      }
      .wx {
        width: 90px;
        margin-left: 30px;
        position: relative;
        .w_text {
          width: 90px;
          height: 29px;
          position: relative;
          font-size: 12px;
          .w_ico {
            width: 36px;
            height: 22px;
            display: inline-block;
            background: url(@images/icon_set.png) center center no-repeat;
            position: absolute;
            top: 5px;
            right: 0px;
            background-position: 0px 0px;
          }
        }
        .w_scan {
          position: absolute;
          left: -220px;
          img {
            border: 1px solid #2a98da;
          }
        }
      }
    }
  }
  .bannerBar {
    position: relative;
    width: 100%;
    .inside_banner {
      width: 1200px;
      margin: 0 auto;
      a {
        display: block;
      }
    }
  }
  .searchBar {
    position: relative;
    width: 100%;
    height: 85px;
    .w1200 {
      height: 85px;
    }
    .logo {
      width: 300px;
    }
    .search {
      width: 514px;
      height: 34px;
      border: 2px solid #fa8d00;
      display: flex;
      .s_icon {
        display: block;
        width: 30px;
        height: 30px;
        border-right: 1px solid #efefef;
        background: url(@images/icon_set.png) no-repeat;
        background-position: -40px 10px;
      }
      .s_input {
        width: 400px;
        padding: 0 4px;
        height: 30px;
        line-height: 30px; /* padding-top:5px\9; height:25px\9;*/
        border: none;
        &:focus {
          outline: none;
        }
      }
      .s_btn {
        width: 80px;
        height: 30px;
        background: #fb8d00;
        color: #fff;
        cursor: pointer;
        border: none;
      }
    }
  }
  .navBar {
    position: relative;
    width: 100%;
    background: #2a98da;
    margin-top: 10px;
    z-index: 3000;
    .w1200 {
      height: 37px;
    }
    .l_menus {
      position: relative;
      width: 300px;
      .top_btn {
        position: relative;
        top: -1px;
      }
      .menus {
        position: absolute;
        top: 47px;
        .menu-item {
          width: 300px;
          height: 87px;
          border-bottom: 1px solid #fff;
          &:last-child {
            border-bottom: none;
          }
          .front {
            width: 290px;
            height: 87px;
            padding-left: 10px;
            background: #fff;
            filter: alpha(opacity=80);
            opacity: 0.8;
            dt {
              font-size: 18px;
              color: #2a98da;
            }
            dd {
              height: 44px;
              width: 221px;
              line-height: 1.5em;
              font-size: 14px;
              overflow: hidden;
              a {
                margin-right: 10px;
                display: inline-block;
                _margin-right: 5px;
                &:hover {
                  color: #f60;
                }
              }
            }
          }
        }
      }
    }
    .r_navs {
      width: 840px;
      height: 37px;
      line-height: 37px;
      display: flex;
      justify-content: space-between;
      ul {
        display: flex;
        justify-content: flex-start;
        padding-left: 20px;
        li {
          width: 90px;
          float: left;
          text-align: center;
          padding-left: 1px;
          padding-right: 1px;
          a {
            color: #fff;
            font-size: 16px;
            display: block;
            &:hover {
              background: #fff;
              border-top: 2px solid #fa8d00;
              height: 37px;
              color: #000;
              cursor: pointer;
            }
            &.on {
              background: #fff;
              border-top: 2px solid #fa8d00;
              height: 37px;
              color: #000;
              cursor: pointer;
            }
          }
        }
      }
      .n_recommend {
        width: 90px;
        height: 35px;
        line-height: 35px;
        position: relative;
        a {
          font-size: 16px;
          color: #fff;
          display: block;
          text-align: center;
          &.on {
            background: #fff;
            border-top: 2px solid #fa8d00;
            color: #000;
            cursor: pointer;
          }
        }
        .recommend_logo {
          width: 22px;
          height: 20px;
          position: absolute;
          top: -14px;
          left: 55px;
          background: url(@images/icon_set.png) no-repeat;
          background-position: -69px 0px;
        }
      }
    }
  }
</style>
