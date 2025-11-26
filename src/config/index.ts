export const TOKEN_TIME: string = 'tokenTime';
export const TOKEN_TIME_VALUE: number = 2 * 60 * 60 * 1000; // 60 * 60 * 1000

const setting = {
  apiRoot:
    process.env.NODE_ENV === 'development' ? 'http://localhost:8081' : 'https://taoapi.sanlidi.com'
};

const site = {
  loginBg: 'food-bg.jpg',
  appId: '',
  appKey: ''
};

export { setting, site };
