const mockRequest = (data: any = '', time = 2000) => {
  return new Promise((resolve) =>
    setTimeout(function () {
      data = data ?? '';
      resolve(data);
    }, time)
  );
};

export default mockRequest;
