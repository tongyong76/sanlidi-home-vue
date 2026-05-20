<template>
  <div class="footer-page">
    <div class="tBar">
      <div class="w1200">
        <div class="l-logo">
          <div class="image"></div>
        </div>
        <div class="slogn" v-for="item in slognList" :key="item.name">
          <div class="slo_img">
            <i :class="item.class"></i>
            <p class="name">{{ item.name }}</p>
          </div>
          <!-- <div
            ><p
              ><b></b><span>{{ item.slogn[0] }}<br />{{ item.slogn[1] }}</span></p
            ></div
          > -->
        </div>
      </div>
    </div>
    <div class="linkBar">
      <div class="w1200">
        <div class="logo">
          <a href="http://www.33ly.com" target="_blank">
            <img src="@images/logo_footer.png" />
          </a>
        </div>
        <div class="r-info">
          <div class="links">
            <a href="http://www.33ly.com/" target="_blank">苏州旅游</a>
            <span>|</span>
            <a href="http://www.33ly.com/" target="_blank">苏州旅行社</a>
            <span>|</span>
            <a href="http://wujiang.33ly.com" target="_blank">吴江旅行社</a>
            <span>|</span>
            <a href="http://changshu.33ly.com" target="_blank">常熟旅行社</a>
            <span>|</span>
            <a href="http://kunshan.33ly.com" target="_blank">昆山旅行社</a>
            <span>|</span>
            <a href="http://taicang.33ly.com" target="_blank">太仓旅行社</a>
            <span>|</span>
            <a href="http://zhangjiagang.33ly.com" target="_blank">张家港旅行社</a>
            <span>|</span>
            <a href="http://www.33ly.com/qianzheng" target="_blank">签证</a>
            <span>|</span>
            <a href="http://www.0512t.net/" target="_blank" rel="nofollow">同行入口</a>
          </div>
          <div class="copyright">
            Copyright © 2007-2017 苏州三三旅游保留所有权 苏ICP备12003954号
          </div>
          <div class="outlet clearfix">
            <div class="mendian fl">
              <div
                class="md_name"
                @mouseenter="handleShowShop(true)"
                @mouseleave="handleShowShop(false)"
              >
                <a href="http://www.33ly.com/about/shiqu.html" target="_blank">
                  查看十八家直营门店>>
                </a>
              </div>
              <div class="md_list" v-if="showShop">
                <div class="tt">所有门店列表</div>
                <ul>
                  <li v-for="item in shopList" :key="item.name">
                    <span class="md_dm">{{ item.name }}：</span>
                    <span class="md_dh">{{ item.tel }}</span>
                    <span class="md_dz">{{ item.address }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref } from 'vue';

  interface IShop {
    name: string;
    tel: string;
    address: string;
  }

  interface ISlogn {
    name: string;
    class: string;
    slogn: [string, string];
  }

  const showShop = ref(false);
  const shopList = ref<IShop[]>([
    {
      name: '市区形象店(总部)',
      tel: '0512-65163333 65163666',
      address: '苏州市东大街488号(东大街与书院巷交界处向南31米)'
    },
    { name: '园区万科美好店', tel: '0512-62961333', address: '园区万科美好广场2楼230B' },
    {
      name: '园区圆融店(台湾馆)',
      tel: '0512-69155333 65723833',
      address: '苏州园区湖东旺敦路圆融购物中心(久光对面)四楼4070号'
    },
    {
      name: '园区星海店',
      tel: '0512-62961333 62962933',
      address: '苏州工业园区星海街168号白领公寓裙楼一楼'
    },
    {
      name: '园区永旺店(邮轮馆)',
      tel: '0512-65731333 68635133',
      address: '园区现代大道与钟南街交汇处永旺梦乐城2楼293'
    },
    {
      name: '桐泾商务广场店',
      tel: '0512-65311333',
      address: '苏州解放东路555号桐泾商务广场2号楼502'
    },
    {
      name: '新区泉屋店',
      tel: '0512-68666633 68188733',
      address: '苏州高新区长江路211号泉屋百货4L-01(泉屋百货4楼VIP服务中心内)'
    },
    {
      name: '相城繁花店(韩国馆)',
      tel: '0512-65232333 65716133',
      address: '苏州相城区人民路4555号繁花商业中心3楼3006'
    },
    {
      name: '吴中丽丰店',
      tel: '0512-65165533 65201933',
      address: '苏州吴中区宝带东路399号丽丰购物中心5楼508'
    },
    {
      name: '吴中永旺店(海岛馆)',
      tel: '0512-65199333 65030233',
      address: '吴中经济开发区苏震桃路188号永旺梦乐城2层265号'
    },
    { name: '吴中万达店', tel: '0512-69131333', address: '吴中万达广场2楼' },
    {
      name: '吴江正翔店(泰国馆)',
      tel: '0512-63431333 63431633',
      address: '吴江松陵镇江兴西路469号正翔商业广场2楼'
    },
    { name: '昆山形象店', tel: '0512-55151333 55153233', address: '昆山市中茵世贸广场百盛外围1楼' },
    {
      name: '昆山世茂店',
      tel: '0512-55115533 55111133',
      address: '昆山世茂东壹号广场前进东路223号'
    },
    {
      name: '常熟旗舰店',
      tel: '0512-52225533 52928933',
      address: '常熟市新世纪大道与珠江路交界处'
    },
    {
      name: '常熟欧尚店',
      tel: '0512-52935333 52968353',
      address: '常熟市黄河路255号欧尚一楼超市入口处'
    },
    {
      name: '太仓华旭广场店',
      tel: '0512-53535333 53875033',
      address: '太仓市人民南路95号华旭广场A218'
    },
    {
      name: '张家港购物公园店',
      tel: '0512-55395533 55395033',
      address: '张家港市购物公园恒光国际2楼'
    },
    { name: '张家港吾悦广场店', tel: '0512-56885933', address: '张家港吾悦广场2楼' }
  ]);
  const slognList = ref<ISlogn[]>([
    {
      name: '自组团',
      class: 'slo_01',
      slogn: ['三三设计、三三收客、三三导游', '三三接送、全程管控，一管到底']
    },
    {
      name: '上门接送',
      class: 'slo_02',
      slogn: ['往返机场上门接送', '体验真正无忧旅途']
    },
    {
      name: '直营门店',
      class: 'slo_03',
      slogn: ['直营门店值得信赖', '报名绝无后顾之忧']
    },
    {
      name: '方便快捷',
      class: 'slo_04',
      slogn: ['门店遍布苏州县市', '方便市民门店咨询']
    },
    {
      name: '维护权益',
      class: 'slo_05',
      slogn: ['关注客户心声', '热线：66667777']
    }
  ]);

  const handleShowShop = (status: boolean) => {
    showShop.value = status;
  };
</script>

<style lang="scss" scoped>
  .w1200 {
    width: 1200px;
    margin: 0 auto;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .tBar {
    width: 100%;
    background: none repeat scroll 0 0 #2a98da;
    position: relative;
    min-width: 1200px;
    height: 66px;
    .l-logo {
      width: 210px;
      height: 66px;
      background: url(@images/icon_set.png) 0px -118px no-repeat;
    }
    .slogn {
      width: 50px;
      position: relative;
      .slo_img {
        width: 50px;
        color: #fff;
        position: relative;
        margin: 0 auto;
        font-size: 12px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        i {
          display: block;
          width: 45px;
          height: 35px;
          background: url(@images/icon_set.png) no-repeat;
        }
        .name {
          margin-top: 4px;
        }
        .slo_01 {
          background-position: 0px -222px;
        }
        .slo_02 {
          background-position: -45px -222px;
        }
        .slo_03 {
          background-position: -90px -222px;
        }
        .slo_04 {
          background-position: -135px -222px;
        }
        .slo_05 {
          background-position: -180px -222px;
        }
      }
    }
    .in_t_r {
      width: 880px;
      padding-top: 5px;
      ul {
        li {
        }
      }
    }
  }
  .linkBar {
    width: 100%;
    background: #f7f7f7;
    position: relative;
    height: 110px;
    .logo {
      width: 323px;
      height: 78px;
      position: relative;
    }
    .r-info {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      align-items: end;
      height: 70px;
      .links {
        height: 20px;
        line-height: 20px;
        span {
          margin: 0px 10px;
          color: #c8c8c8;
        }
        .friendLink {
          position: relative;
          .flink_title {
          }
          .flink_list {
            position: absolute;
            bottom: 20px;
            right: 0px;
            background: #fff;
            display: none;
            .wipt {
              width: 200px;
              height: 300px;
              border: 1px solid #c8c8c8;
              overflow-y: scroll;
              a {
                display: block;
                height: 30px;
                line-height: 30px;
                color: #2a98da;
                padding: 0 10px;
                &:hover {
                  background: #178fd5;
                  color: #fff;
                }
              }
            }
            p {
              width: 200px;
              font-size: 12px;
              text-align: center;
              background: #f1f1f1;
              border: 1px solid #c8c8c8;
              border-top: none;
              height: 25px;
              line-height: 25px;
              _overflow: hidden;
            }
          }
        }
      }
      .copyright {
        color: #333;
        height: 20px;
        line-height: 20px;
        span {
          position: absolute;
          top: 0;
          margin-left: 10px;
          a {
          }
          img {
            height: 55px;
          }
        }
      }
      .outlet {
        position: relative;
        height: 20px;
        line-height: 20px;
        .md_list {
          width: 730px;
          position: absolute;
          bottom: 27px;
          left: -400px;
          border: 1px solid #f1f1f1;
          background: #fff;
          .tt {
            padding: 10px 0 0 20px;
            font-weight: bold;
          }
          h1 {
            height: 30px;
            line-height: 30px;
            padding-left: 10px;
            font-size: 14px;
            background: #f1f1f1;
          }
          ul {
            li {
              padding: 0px 20px;
              font-size: 12px;
              .md_dm {
                width: 130px;
                display: inline-block;
                margin-right: 8px;
              }
              .md_dh {
                margin-right: 8px;
              }
            }
          }
        }
      }
    }
  }
  .demopage {
    width: 880px;
    padding-top: 8px;
    height: 52px;
    _overflow: hidden;
  }
  .sidenav li {
    position: relative;
    float: left;
    width: 176px;
  }
  .sidenav li .slo_img {
    width: 50px;
    color: #fff;
    position: relative;
    margin: 0 auto;
    font-size: 12px;
    text-align: center;
  }
  .sidenav li .slo_img i {
    display: block;
    width: 45px;
    height: 35px;
    background: url(@images/icon_set.png) no-repeat;
  }
  .sidenav li .slo_img .slo_01 {
    background-position: 0px -222px;
  }
  .sidenav li .slo_img .slo_02 {
    background-position: -45px -222px;
  }
  .sidenav li .slo_img .slo_03 {
    background-position: -90px -222px;
  }
  .sidenav li .slo_img .slo_04 {
    background-position: -135px -222px;
  }
  .sidenav li .slo_img .slo_05 {
    background-position: -180px -222px;
  }
  .sidenav li div {
    display: none;
    position: absolute;
    top: 0;
    left: 0;
    font-size: 12px;
    width: 206px;
    color: #fff;
  }
  .sidenav li div p span {
    background: #94caec;
    display: inline-block;
    padding: 5px;
  }
  .sidenav li div p b {
    width: 7px;
    height: 11px;
    background: url(../images/icon_set.png) -90px -32px no-repeat;
    position: absolute;
    top: 20px;
    left: -7px;
  }
</style>
