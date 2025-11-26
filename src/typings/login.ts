export interface IPhoneLogin {
  phone: string;
  password: string;
}

export interface IPhoneLoginResponse {
  code: number;
  token?: string;
  message?: string;
}
