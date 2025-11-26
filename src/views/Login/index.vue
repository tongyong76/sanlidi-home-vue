<template>
  <div class="login">
    <div class="mask">
      <div class="bg-image"></div>
      <div class="spot"></div>
    </div>
    <div class="body">
      <div class="login-form fadeInLeft">
        <div class="header">
          <img src="@/assets/images/cms-logo.png" alt="SanAdmin" />
          <p>SanAdmin后台管理系统</p>
        </div>
        <ElForm
          ref="formRef"
          class="el-form"
          :model="loginFormData"
          :rules="rules"
          v-loading.fullscreen.lock="loading"
          element-loading-background="rgba(0, 0, 0, 0.7)"
        >
          <ElFormItem class="form-item-input" prop="phone">
            <el-input
              size="large"
              v-model="loginFormData.phone"
              placeholder="请输入手机号"
              :prefix-icon="User"
            >
            </el-input>
          </ElFormItem>
          <ElFormItem class="form-item-input" prop="password">
            <el-input
              size="large"
              v-model="loginFormData.password"
              type="password"
              placeholder="请输入密码"
              :prefix-icon="Lock"
            >
            </el-input>
          </ElFormItem>
          <ElFormItem class="form-item-input">
            <SanSliderCheck
              ref="sliderCheckRef"
              @slider-check-success="handleSuccessCheckSuccess"
              @slider-check-fail="handleErrorCheckFail"
            ></SanSliderCheck>
          </ElFormItem>
          <ElFormItem>
            <el-button
              size="large"
              class="full-btn"
              type="primary"
              @click="loginAction(formRef)"
              auto-insert-space
              >登录</el-button
            >
          </ElFormItem>
        </ElForm>
      </div>
    </div>
    <div class="footer">
      <p>
        Copyright © <span class="js-year-copy">2011-2021</span>
        <a class="link" href="http://www.szaws.com" target="_blank">云巢网络技术有限公司</a>
        All Rights Reserved.
      </p>
    </div>
  </div>
</template>
<script setup lang="ts">
  import { onMounted, ref, reactive } from 'vue';
  import { useRouter } from 'vue-router';
  import { User, Lock } from '@element-plus/icons-vue';
  import { ElForm, ElFormItem, type FormInstance, type FormRules } from 'element-plus';
  import Message from '@/utils/message';
  import { loginByPhone } from '@/api/login';
  import { useAppStore } from '@/store/main';
  import md5 from 'md5';

  const router = useRouter();
  const store = useAppStore();
  const loading = ref(false);
  const isOk = ref(false);
  const formRef = ref<FormInstance>();
  const loginFormData = reactive({
    phone: '18662207666',
    password: 'admin123'
  });
  const rules = reactive<FormRules<any>>({
    phone: [
      {
        required: true,
        message: '请输入手机号',
        trigger: 'blur'
      },
      {
        pattern: /^1(3[0-9]|4[01456879]|5[0-35-9]|6[2567]|7[0-8]|8[0-9]|9[0-35-9])\d{8}$/,
        // min: 3,
        // max: 5,
        message: '请输入正确的手机号',
        trigger: 'blur'
      }
    ],
    password: [
      {
        required: true,
        message: '密码不能为空',
        trigger: 'blur'
      }
    ]
  });
  const sliderCheckRef = ref<any>(null);

  onMounted(() => {
    console.log('login onMounted');
  });

  // 滑块成功触发
  const handleSuccessCheckSuccess = () => {
    isOk.value = true;
  };

  // 滑块失败触发
  const handleErrorCheckFail = () => {
    console.log('滑块验证失败');
  };

  // 登录
  const loginAction = async (formEl: FormInstance | undefined) => {
    if (!formEl || !isOk.value) {
      Message.error('请先拖动滑块验证');
      return;
    }
    formEl.validate((valid) => {
      if (valid) {
        // 成功
        loading.value = true;
        const { phone, password } = loginFormData;

        loginByPhone({ phone: phone, password: md5(password) })
          .then((res: any) => {
            loading.value = false;
            if (res.code < 0) {
              // isOk.value = false;
            } else {
              store.token = res.data.token;
              store.isLogin = true;
              Message.success('登录成功');
              setTimeout(() => {
                router.push({ path: '/' });
              }, 500);
            }
          })
          .catch((e) => {
            Message.error(e.message);
            setTimeout(() => {
              loading.value = false;
              sliderCheckRef.value.resetFun(); //清空slider验证
              isOk.value = false;
              formEl.resetFields(); //重置表单
            }, 1500);
            Message.error('接口错误，请稍后再试');
            console.log('login fail');
          });
      } else {
        // 失败
        sliderCheckRef.value.resetFun();
      }
    });
  };
</script>

<style lang="scss" scoped>
  .login {
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
    overflow: hidden;
    background-size: cover;
    background-image: url(@/assets/images/food-bg.jpg);
    .mask {
      width: 100%;
      height: 100%;
      .bg-image {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1;
        background-color: rgba(0, 0, 0, 0.95);
        background-image: -webkit-linear-gradient(left, transparent 0, #1b1f27 100%);
        background-image: -o-linear-gradient(left, transparent 0, #1b1f27 100%);
        background-image: linear-gradient(to right, transparent 0, #1b1f27 100%);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000', endColorstr='#ff1b1f27', GradientType=1);
        opacity: 0.3;
      }
      .spot {
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 2;
        background-image: url(@/assets/images/dot.png);
      }
    }
    .body {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }
    .footer {
      position: fixed;
      left: 0;
      bottom: 0;
      width: 100%;
      line-height: 30px;
      padding: 20px;
      text-align: center;
      box-sizing: border-box;
      color: rgba(255, 255, 255, 0.5);
      z-index: 3;
      a {
        color: rgba(255, 255, 255, 0.8);
        font-size: 13px;
        padding: 0 4px;
      }
    }
  }
  .body {
    .login-form {
      position: absolute;
      width: 400px;
      top: 50%;
      left: 50%;
      margin-left: -200px;
      margin-top: -250px;
      padding: 36px;
      box-sizing: border-box;
      border-radius: 4px;
      background: #fff;
      z-index: 3;
      &.fadeInLeft {
        animation-name: fadeInLeft;
        -webkit-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        animation-fill-mode: both;
      }
      .header {
        text-align: center;
        img {
          width: 100px;
          height: 100px;
          border-radius: 2000px;
          box-shadow: 0 0 5px #666;
          background: #fff;
        }
        p {
          font-weight: 300;
          color: #999;
          margin-top: 10px;
        }
      }
      .el-form {
        margin-top: 20px;
        .form-item-input {
          .iconfont {
            margin-left: 4px;
          }
        }
        .full-btn {
          width: 100%;
          background: #409eff;
          border-color: #409eff;
          &:hover {
            background: #66b1ff;
            border-color: #66b1ff;
          }
        }
      }
    }
  }
  @-webkit-keyframes fadeInLeft {
    0% {
      opacity: 0;
      -webkit-transform: translateX(-20px);
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      -webkit-transform: translateX(0);
      transform: translateX(0);
    }
  }
  @keyframes fadeInLeft {
    0% {
      opacity: 0;
      -webkit-transform: translateX(-20px);
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      -webkit-transform: translateX(0);
      transform: translateX(0);
    }
  }
</style>
