<template>
  <div class="index-page">
    <!--banner-->
    <div class="index-banner">
      <Banner :items="banners"></Banner>
    </div>
    <div class="index-hot">
      <div class="w1200">
        <div class="left">
          <div class="tejia">
            <div class="tejia_title">
              <span>每周特价</span>抢到就是赚到
              <!--<a class="fr" href="#" target="_blank">更多>></a>-->
            </div>
            <ul class="tejia_items">
              <li v-for="item in tj" :key="item.id">
                <div class="twi_left fl"
                  ><a href="#"><img :src="IMAGE_URL + item.imgurl" /></a><span></span
                ></div>
                <div class="twi_right fl">
                  <div class="twi_title"
                    ><a href="#">{{ item.name }}</a></div
                  >
                  <div class="twi_des">立减{{ item.market_price - item.minprice }}元</div>
                  <div class="twi_price"
                    >￥<span>{{ item.minprice }}</span
                    >起<a class="twip_go fr" href="#"></a
                  ></div>
                </div>
              </li>
            </ul>
          </div>
          <div class="xinwen">
            <div class="x_w_title">
              <span>三三新闻</span>
              <a href="#" target="_blank">更多>></a>
            </div>
            <div class="x_w_item">
              <ul>
                <li v-for="item in news" :key="item.id">
                  <div class="xwi_left"
                    ><a href="#" target="_blank">{{ item.title }}</a></div
                  >
                  <div class="xwi_right">{{ item.add_time }}</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="right">
          <div class="products">
            <div class="h_title">
              <p class="h_title_n">
                <span class="h_t">主推产品</span>
                <span class="h_youlun"
                  ><strong>沪上邮轮特价：</strong
                  ><a href="#" target="_blank"
                    >【10月20日天海新世纪 济州-:釜山(过夜)5晚6天1599元/人】</a
                  ></span
                >
                <span class="change">
                  <a href="javascript:void(0);">更多线路</a>
                  <input type="hidden" name="start" value="0" />
                </span>
              </p>
            </div>
            <ul class="h_list">
              <li class="h_list_item" v-for="item in hot" :key="item.id">
                <a href="{:C('site_domain')}/{$vo.pinyin}/xianlu-{$vo.id}.html" target="_blank">
                  <dl class="clearfix">
                    <dt>
                      <p class="h_n_name"
                        ><span class="h_n_link"> <{{ item.name }}> </span
                        ><span class="h_n_shortdes">{{ item.subname }}</span>
                        <!-- <if condition="in_array(6,$vo['switch'])"
                          ><span class="mark">{:C('switch.6')}</span></if
                        > -->
                      </p>
                      <p class="h_n_description">{{ item.info }}</p>
                    </dt>
                    <dd>
                      <p class="h_price">
                        ￥<span class="price">{{ item.minprice }}</span
                        >起
                      </p>
                    </dd>
                  </dl>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!--main-floor-1-->
    <div class="main-floor">
      <div class="left-ad">
        <div class="title-box">
          <p><span>出境游</span>轻松玩转全球</p>
        </div>
        <div class="image-box">
          <a :href="ads.sycj1?.link" class="img1" target="_blank">
            <img :src="IMAGE_URL + ads.sycj1?.imgurl" alt="苏州旅游|苏州出境游|苏州到泰国旅游" />
          </a>
          <a :href="ads.sycj2?.link" class="img2" target="_blank">
            <img :src="IMAGE_URL + ads.sycj2?.imgurl" alt="苏州旅游|苏州出境游|苏州邮轮游" />
          </a>
          <a :href="ads.sycj3?.link" class="img2" target="_blank">
            <img :src="IMAGE_URL + ads.sycj3?.imgurl" alt="苏州旅游|苏州出境游|苏州到台湾旅游" />
          </a>
        </div>
      </div>
      <div class="right-goods">
        <div class="title-box">
          <ul class="sel_name" id="out_sel_name">
            <li><a href="javascript:void(0)" class="sel_route">精选</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#" class="noborder">分类</a></li>
          </ul>
          <label class="outboundBg"></label>
          <a href="#" class="more_route" target="_blank">更多线路>></a>
        </div>
        <div class="goods-box">
          <div class="main_part_item">
            <ul class="main_part_it">
              <li
                :class="index == 2 ? 'nomar' : ''"
                v-for="(item, index) in floor?.chujing.slice(0, 3)"
                :key="item.id"
              >
                <div class="m_p_pic">
                  <a
                    :href="`/chujing/xianlu-${item.id}.html`"
                    target="_blank"
                    :title="item.name + item.subname"
                    ><img class="lazy" :src="IMAGE_URL + item.imgurl"
                  /></a>
                </div>
                <div class="m_p_bg"></div>
                <div class="m_p_des">
                  <div class="m_p_name">
                    <a :href="`/chujing/xianlu-${item.id}.html`" target="_blank">
                      <em> <{{ item.name }}> </em>
                      {{ item.subname }}{{ index }}
                    </a>
                  </div>
                  <div class="m_p_price">
                    ￥
                    <span>{{ item.minprice }}</span>
                    <i>起</i>
                  </div>
                </div>
              </li>
            </ul>
            <div class="main_part_line clearfix">
              <ul class="m_line_left">
                <li v-for="item in floor?.chujing.slice(3, 7)" :key="item.id">
                  <a class="line_des" :href="IMAGE_URL + item.imgurl" target="_blank">
                    <{{ item.name }}>{{ item.subname }}
                  </a>
                  <span class="line_price"
                    >￥<span class="l_p">{{ item.minprice }}</span
                    ><i>起</i></span
                  >
                </li>
              </ul>
              <ul class="m_line_left noborright">
                <li v-for="item in floor?.chujing.slice(7, 11)" :key="item.id">
                  <a class="line_des" :href="IMAGE_URL + item.imgurl" target="_blank">
                    <{{ item.name }}>{{ item.subname }}
                  </a>
                  <span class="line_price"
                    >￥<span class="l_p">{{ item.minprice }}</span
                    ><i>起</i></span
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--main-floor-2-->
    <div class="main-floor">
      <div class="left-ad">
        <div class="title-box s2">
          <p><span>国内游</span>行遍神州大地</p>
        </div>
        <div class="image-box">
          <a :href="ads.sycj1?.link" class="img1" target="_blank">
            <img :src="IMAGE_URL + ads.sygn1?.imgurl" alt="苏州旅游|苏州出境游|苏州到泰国旅游" />
          </a>
          <a :href="ads.sycj2?.link" class="img2" target="_blank">
            <img :src="IMAGE_URL + ads.sygn2?.imgurl" alt="苏州旅游|苏州出境游|苏州邮轮游" />
          </a>
          <a :href="ads.sycj3?.link" class="img2" target="_blank">
            <img :src="IMAGE_URL + ads.sygn3?.imgurl" alt="苏州旅游|苏州出境游|苏州到台湾旅游" />
          </a>
        </div>
      </div>
      <div class="right-goods">
        <div class="title-box s2">
          <ul class="sel_name" id="out_sel_name">
            <li><a href="javascript:void(0)" class="sel_route s2">精选</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#" class="noborder">分类</a></li>
          </ul>
          <label class="outboundBg s2"></label>
          <a href="#" class="more_route" target="_blank">更多线路>></a>
        </div>
        <div class="goods-box">
          <div class="main_part_item">
            <ul class="main_part_it">
              <li
                :class="index == 2 ? 'nomar' : ''"
                v-for="(item, index) in floor?.guonei.slice(0, 3)"
                :key="item.id"
              >
                <div class="m_p_pic">
                  <a
                    :href="`/guonei/xianlu-${item.id}.html`"
                    target="_blank"
                    :title="item.name + item.subname"
                    ><img class="lazy" :src="IMAGE_URL + item.imgurl"
                  /></a>
                </div>
                <div class="m_p_bg"></div>
                <div class="m_p_des">
                  <div class="m_p_name">
                    <a :href="`/guonei/xianlu-${item.id}.html`" target="_blank">
                      <em> <{{ item.name }}> </em>
                      {{ item.subname }}{{ index }}
                    </a>
                  </div>
                  <div class="m_p_price">
                    ￥
                    <span>{{ item.minprice }}</span>
                    <i>起</i>
                  </div>
                </div>
              </li>
            </ul>
            <div class="main_part_line clearfix">
              <ul class="m_line_left">
                <li v-for="item in floor?.guonei.slice(3, 7)" :key="item.id">
                  <a class="line_des" :href="`/guonei/xianlu-${item.id}.html`" target="_blank">
                    <{{ item.name }}>{{ item.subname }}
                  </a>
                  <span class="line_price"
                    >￥<span class="l_p">{{ item.minprice }}</span
                    ><i>起</i></span
                  >
                </li>
              </ul>
              <ul class="m_line_left noborright">
                <li v-for="item in floor?.guonei.slice(7, 11)" :key="item.id">
                  <a class="line_des" :href="`/guonei/xianlu-${item.id}.html`" target="_blank">
                    <{{ item.name }}>{{ item.subname }}
                  </a>
                  <span class="line_price"
                    >￥<span class="l_p">{{ item.minprice }}</span
                    ><i>起</i></span
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--main-floor-3-->
    <div class="main-floor">
      <div class="left-ad">
        <div class="title-box s3">
          <p><span>周边游</span>江浙沪皖随意游</p>
        </div>
        <div class="image-box">
          <a :href="ads.sycj1?.link" class="img1" target="_blank">
            <img :src="IMAGE_URL + ads.syzb1?.imgurl" alt="苏州旅游|苏州出境游|苏州到泰国旅游" />
          </a>
          <a :href="ads.sycj2?.link" class="img2" target="_blank">
            <img :src="IMAGE_URL + ads.syzb2?.imgurl" alt="苏州旅游|苏州出境游|苏州邮轮游" />
          </a>
          <a :href="ads.sycj3?.link" class="img2" target="_blank">
            <img :src="IMAGE_URL + ads.syzb3?.imgurl" alt="苏州旅游|苏州出境游|苏州到台湾旅游" />
          </a>
        </div>
      </div>
      <div class="right-goods">
        <div class="title-box s3">
          <ul class="sel_name" id="out_sel_name">
            <li><a href="javascript:void(0)" class="sel_route s3">精选</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#">分类</a></li>
            <li><a target="_blank" href="#" class="noborder">分类</a></li>
          </ul>
          <label class="outboundBg s3"></label>
          <a href="#" class="more_route" target="_blank">更多线路>></a>
        </div>
        <div class="goods-box">
          <div class="main_part_item">
            <ul class="main_part_it">
              <li
                :class="index == 2 ? 'nomar' : ''"
                v-for="(item, index) in floor?.zhoubian.slice(0, 3)"
                :key="item.id"
              >
                <div class="m_p_pic">
                  <a
                    :href="`/zhoubian/xianlu-${item.id}.html`"
                    target="_blank"
                    :title="item.name + item.subname"
                    ><img class="lazy" :src="IMAGE_URL + item.imgurl"
                  /></a>
                </div>
                <div class="m_p_bg"></div>
                <div class="m_p_des">
                  <div class="m_p_name">
                    <a :href="`/zhoubian/xianlu-${item.id}.html`" target="_blank">
                      <em> <{{ item.name }}> </em>
                      {{ item.subname }}{{ index }}
                    </a>
                  </div>
                  <div class="m_p_price">
                    ￥
                    <span>{{ item.minprice }}</span>
                    <i>起</i>
                  </div>
                </div>
              </li>
            </ul>
            <div class="main_part_line clearfix">
              <ul class="m_line_left">
                <li v-for="item in floor?.chujing.slice(3, 7)" :key="item.id">
                  <a class="line_des" :href="`/zhoubian/xianlu-${item.id}.html`" target="_blank">
                    <{{ item.name }}>{{ item.subname }}
                  </a>
                  <span class="line_price"
                    >￥<span class="l_p">{{ item.minprice }}</span
                    ><i>起</i></span
                  >
                </li>
              </ul>
              <ul class="m_line_left noborright">
                <li v-for="item in floor?.chujing.slice(7, 11)" :key="item.id">
                  <a class="line_des" :href="`/zhoubian/xianlu-${item.id}.html`" target="_blank">
                    <{{ item.name }}>{{ item.subname }}
                  </a>
                  <span class="line_price"
                    >￥<span class="l_p">{{ item.minprice }}</span
                    ><i>起</i></span
                  >
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import { router } from '@/router';
  import Banner from '@/views/Components/Banner/index.vue';
  import { getIndexBanner } from '@/api/banner';
  import { getIndexTj, getIndexHot, getIndexFloor } from '@/api/goods';
  import { getIndexNews } from '@/api/article';
  import { getAd } from '@/api/ad';
  import { IBannerItem } from '@/types/banner';
  import { IMAGE_URL } from '@/config';

  const banners = ref<IBannerItem[]>([]);
  const tj = ref<any[]>([]);
  const hot = ref<any[]>([]);
  const news = ref<any[]>([]);
  const ads = ref<any>({});
  const floor = ref<{ chujing: any[]; guonei: any[]; zhoubian: any[] }>();

  onMounted(async () => {
    banners.value = await getIndexBanner();
    tj.value = await getIndexTj();
    hot.value = await getIndexHot();
    news.value = await getIndexNews();
    ads.value = await getAd();
    floor.value = await getIndexFloor();
  });
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
  .index-banner {
    width: 1200px;
    height: 350px;
    margin: 0 auto;
    position: relative;
    overflow: visible !important;
  }
  .index-hot {
    width: 100%;
    margin-top: 20px;
    position: relative;
    .w1200 {
      height: 360px;
    }
    .left {
      width: 300px;
    }
    .right {
      width: 880px;
    }
  }
  .tejia {
    height: 220px;
    overflow: hidden;
    .tejia_title {
      height: 38px;
      line-height: 38px;
      color: #808080;
      position: relative;
    }
    .tejia_items {
      height: 160px;
      border: 1px solid #eee;
      padding: 10px 5px;
      li {
        margin-bottom: 15px;
        .twi_left {
          width: 125px;
          height: 72px;
          position: relative;
          a {
            display: block;
            height: 100%;
            img {
              display: block;
              width: 100%;
              height: 100%;
            }
          }
          span {
            position: absolute;
            top: -5px;
            left: 5px;
            width: 33px;
            height: 37px;
            background: url(@images/icon_set.png) 0px -263px no-repeat;
          }
        }
        .twi_right {
          width: 140px;
          height: 72px;
          padding: 0px 10px;
          overflow: hidden;
          .twi_title {
            height: 25px;
            line-height: 25px;
            overflow: hidden;
          }
          .twi_des {
            height: 22px;
            line-height: 20px;
            color: #999;
          }
          .twi_price {
            height: 25px;
            line-height: 25px;
            color: #f60;
            font-size: 12px;
            position: relative;
            span {
              color: #f60;
              font-family: georgia;
              font-size: 18px;
              .twip_go {
                width: 64px;
                height: 22px;
                background: url(@images/icon_set.png) -45px -263px no-repeat;
                position: absolute;
                right: 0;
              }
              .twip_out {
                width: 64px;
                height: 22px;
                background: url(@images/icon_set.png) -135px -263px no-repeat;
              }
            }
          }
        }
      }
    }
  }
  .xinwen {
    height: 140px;
    overflow: hidden;
    .x_w_title {
      height: 38px;
      line-height: 38px;
      padding: 0 10px;
      font-weight: bold;
      color: #808080;
      position: relative;
      display: flex;
      justify-content: space-between;
      span {
        display: block;
        font-size: 20px;
        color: #333;
        margin-right: 5px;
        font-weight: normal;
      }
      a {
        display: block;
        color: #808080;
        font-weight: normal;
        color: #333;
        &:hover {
          color: #ff6600;
        }
      }
    }
    .x_w_item {
      height: 90px;
      border: 1px solid #eee;
      padding: 10px;
      color: #333;
      ul {
        height: 75px;
        overflow: hidden;
        li {
          height: 25px;
          line-height: 25px;
          overflow: hidden;
          display: flex;
          justify-content: space-between;
          .xwi_left {
            width: 210px;
            height: 25px;
            overflow: hidden;
            a {
              font-size: 13px;
            }
          }
          .xwi_right {
            width: 58px;
            font-size: 13px;
          }
        }
      }
    }
  }
  .products {
    .h_title {
      height: 37px;
      line-height: 37px;
      border-bottom: 1px solid #2a98da;
      .h_title_n {
        height: 37px;
        position: relative;
      }
      .h_t {
        font-size: 25px;
        font-weight: bold;
        color: #2a98da;
        margin-right: 5px;
      }
      .h_youlun {
        margin-left: 50px;
        font-size: 14px;
        strong {
          color: red;
        }
      }
      .change {
        position: absolute;
        top: 0px;
        right: 0px;
        a {
          background: url(@images/change_ico.jpg) left center no-repeat;
          padding-left: 20px;
        }
      }
    }
    .h_list {
      height: 315px;
      overflow: hidden;
      li {
        height: 63px;
        border-bottom: 1px dashed #eeeeee;
        line-height: 63px;
        a {
          display: block;
          cursor: pointer;
          color: #2a98da;
          &:hover {
            color: #f60;
          }
        }
        dl {
          display: flex;
          justify-content: space-between;
          dt {
            width: 630px;
            .h_n_name {
              height: 35px;
              line-height: 35px;
              position: relative;
              font-size: 16px;
              font-weight: bold;
              .h_n_link {
              }
              .h_n_shortdes {
                font-size: 14px;
              }
              .mark {
                margin-left: 10px;
                width: 48px;
                height: 18px;
                border-radius: 5px;
                background: #f60;
                display: inline-block;
                text-align: center;
                color: #fff;
                line-height: 18px;
                font-size: 12px;
              }
            }
            .h_n_description {
              height: 28px;
              line-height: 28px;
              font-size: 13px;
              color: #333;
            }
          }
          dd {
            width: 250px;
            .h_price {
              height: 63px;
              text-align: right;
              color: #f60;
              .price {
                font-size: 25px;
                font-family: georgia;
              }
            }
          }
        }
      }
    }
  }
  .main-floor {
    width: 1200px;
    height: 420px;
    position: relative;
    margin: 0 auto;
    margin-top: 30px;
    display: flex;
    justify-content: space-between;
    .left-ad {
      width: 300px;
      .title-box {
        border-bottom: 2px solid #ff7c80;
        &.s2 {
          border-bottom: 2px solid #ba92dd;
        }
        &.s3 {
          border-bottom: 2px solid #9bc626;
        }
        p {
          height: 40px;
          line-height: 40px;
          color: #808080;
          span {
            font-size: 24px;
            font-weight: bold;
            color: #ff7c80;
            margin-right: 5px;
          }
        }
      }
      .image-box {
        width: 300px;
        .img1 {
          height: 230px;
          position: relative;
          display: block;
          img {
            position: absolute;
            right: 0px;
          }
        }
        .img2 {
          height: 73px;
          border-bottom: 1px solid #eee;
          display: block;
        }
      }
    }
    .right-goods {
      width: 900px;
      .title-box {
        height: 42px;
        line-height: 42px;
        border-bottom: 2px solid #ff7c80;
        position: relative;
        display: flex;
        justify-content: space-between;
        &.s2 {
          border-bottom: 2px solid #ba92dd;
        }
        &.s3 {
          border-bottom: 2px solid #9bc626;
        }
        .sel_name {
          li {
            float: left;
            margin-right: 10px;
            height: 40px;
            a {
              border-right: 1px solid #eee;
              padding-right: 5px;
            }
            .sel_route {
              background: #ff7c80;
              border-bottom: 2px solid #ff7c80;
              padding: 5px 10px;
              color: #fff;
              font-size: 14px;
              &.s2 {
                background: #ba92dd;
                border-bottom: 2px solid #ba92dd;
              }
              &.s3 {
                background: #9bc626;
                border-bottom: 2px solid #9bc626;
              }
            }
            .noborder {
              border: 0px;
            }
          }
        }
        .outboundBg {
          width: 169px;
          height: 30px;
          position: absolute;
          right: 80px;
          bottom: 0px;
          background: url(@images/icon_set.png) 0px -75px no-repeat;
          &.s2 {
            width: 172px;
            height: 40px;
            background: url(@images/icon_set.png) -178px -75px no-repeat;
          }
          &.s3 {
            width: 172px;
            height: 34px;
            background: url(@images/icon_set.png) -361px -75px no-repeat;
          }
        }
      }
    }
    .goods-box {
      padding-top: 13px;
      .main_part_it {
        height: 236px;
        border-bottom: 1px solid #dedede;
        margin-left: 10px;
        li {
          width: 280px;
          height: 236px;
          display: inline;
          float: left;
          margin-right: 20px;
          overflow: hidden;
          position: relative;
          .m_p_pic {
            a {
              display: block;
              width: 280px;
              height: 175px;
              overflow: hidden;
              img {
                display: block;
                margin: 0 auto;
                width: 100%;
                height: 100%;
              }
            }
          }
          .m_p_bg {
            width: 202px;
            height: 88px;
            position: absolute;
            left: 40px;
            top: 145px;
            background: #fff;
          }
          .m_p_des {
            width: 202px;
            height: 88px;
            position: absolute;
            left: 40px;
            top: 145px;
            background: #fff;
            text-align: center;
            .m_p_name {
              margin: 10px 10px 5px;
              height: 40px;
              overflow: hidden;
              a {
                color: #808080;
                font-size: 14px;
                display: block;
                &:hover {
                  font-weight: 600;
                }
                em {
                  color: #000000;
                }
              }
            }
            .m_p_price {
              color: #f60;
              font-size: 14px;
              text-align: center;
              span {
                font-size: 20px;
                font-family: georgia;
              }
              i {
                font-size: 12px;
              }
            }
          }
        }
        .nomar {
          margin: 0px;
        }
      }
      .main_part_line {
        padding-top: 10px;
        .m_line_left {
          float: left;
          border-right: 1px dashed #eee;
          padding: 0px 10px;
          li {
            width: 419px;
            height: 30px;
            line-height: 30px;
            overflow: hidden;
            font-size: 14px;
            .line_des {
              width: 300px;
              float: left;
              overflow: hidden;
            }
            .line_price {
              height: 30px;
              float: right;
              color: #f60;
              .l_p {
                font-size: 18px;
                margin-right: 3px;
                font-family: georgia;
              }
              i {
                font-size: 11px;
              }
            }
          }
        }
        .noborright {
          border-right: 0px;
        }
      }
    }
  }
</style>
