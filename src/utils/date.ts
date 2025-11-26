/**
 * 获取时间戳 13位
 * @param day string 'xxxx-xx-xx'
 * @param flag boolean true:time false:time/1000
 */
export const getTimeStamp = () => {
  const getTimeStamp = new Date().getTime();
  return getTimeStamp;
};

/**
 * 返回 xxxx-xx-xx
 */
export const makeDateDir = () => {
  const date = new Date();
  const yyyy = date.getFullYear();
  const mm = date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
  const dd = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
  return `${yyyy}${mm}${dd}`;
};

export const makeDate = (timestamp: number | null = null) => {
  let date: Date;
  if (timestamp) {
    date = new Date(timestamp * 1000);
  } else {
    date = new Date();
  }
  const year = date.getFullYear();
  const month = date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1;
  const day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
  const hh = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
  const mm = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
  const ss = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
  return `${year}-${month}-${day} ${hh}:${mm}:${ss}`;
};

// 返回 1212-12-12 00:00
export const getDayTime = (timestamp: number) => {
  const time = new Date(timestamp * 1000);
  const year = time.getFullYear();
  const month = time.getMonth() + 1;
  const day = time.getDate();
  const hour = time.getHours();
  const minute = time.getMinutes();

  return (
    year +
    '-' +
    (month < 10 ? '0' + month : month) +
    '-' +
    (day < 10 ? '0' + day : day) +
    ' ' +
    (hour < 10 ? '0' + hour : hour) +
    ':' +
    (minute < 10 ? '0' + minute : minute)
  );
};

/**
 * 返回2012-12-12
 * @param time 时间戳
 * @returns
 */
export const getDate = (time: Date) => {
  const year = time.getFullYear();
  const month = time.getMonth() + 1 < 10 ? '0' + (time.getMonth() + 1) : time.getMonth() + 1;
  const day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
  return `${year}-${month}-${day}`;
};

// 时间范围
export const shortcuts = [
  {
    text: '今天',
    value: () => {
      const end = new Date();
      const start = new Date();
      start.setTime(
        new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate()).getTime()
      );
      return [start, end];
    }
  },
  {
    text: '昨天',
    value: () => {
      const end = new Date();
      const start = new Date();
      start.setTime(
        start.setTime(
          new Date(
            new Date().getFullYear(),
            new Date().getMonth(),
            new Date().getDate() - 1
          ).getTime()
        )
      );
      end.setTime(
        end.setTime(
          new Date(
            new Date().getFullYear(),
            new Date().getMonth(),
            new Date().getDate()
          ).getTime() - 1
        )
      );
      return [start, end];
    }
  },
  {
    text: '本月',
    value: () => {
      const end = new Date();
      const start = new Date();
      start.setTime(
        start.setTime(new Date(new Date().getFullYear(), new Date().getMonth(), 1).getTime())
      );
      return [start, end];
    }
  },
  {
    text: '上月',
    value: () => {
      const end = new Date();
      const start = new Date();
      let currentDate = new Date();
      let currentYear = currentDate.getFullYear();
      let currentMonth = currentDate.getMonth();
      start.setTime(new Date(currentYear, currentMonth - 1, 1).getTime());
      end.setTime(new Date(currentYear, currentMonth, 1).getTime() - 1);
      return [start, end];
    }
  },
  {
    text: '最近7天',
    value: () => {
      const end = new Date();
      const start = new Date();
      let currentYear = end.getFullYear();
      let currentMonth = end.getMonth();
      let currentDay = end.getDate();
      start.setTime(
        new Date(currentYear, currentMonth, currentDay).getTime() - 3600 * 1000 * 24 * 6 - 1
      );
      return [start, end];
    }
  },
  {
    text: '最近30天',
    value: () => {
      const end = new Date();
      const start = new Date();
      let currentYear = end.getFullYear();
      let currentMonth = end.getMonth();
      let currentDay = end.getDate();
      start.setTime(
        new Date(currentYear, currentMonth, currentDay).getTime() - 3600 * 1000 * 24 * 29 - 1
      );
      return [start, end];
    }
  },
  {
    text: '最近90天',
    value: () => {
      const end = new Date();
      const start = new Date();
      let currentYear = end.getFullYear();
      let currentMonth = end.getMonth();
      let currentDay = end.getDate();
      start.setTime(
        new Date(currentYear, currentMonth, currentDay).getTime() - 3600 * 1000 * 24 * 89 - 1
      );
      return [start, end];
    }
  },
  {
    text: '最近1年',
    value: () => {
      const end = new Date();
      const start = new Date();
      let currentYear = end.getFullYear();
      let currentMonth = end.getMonth();
      let currentDay = end.getDate();
      start.setTime(
        new Date(currentYear, currentMonth, currentDay).getTime() - 3600 * 1000 * 24 * 364 - 1
      );
      return [start, end];
    }
  },
  {
    text: '今年',
    value: () => {
      const end = new Date();
      const start = new Date();
      start.setTime(start.setTime(new Date(new Date().getFullYear(), 0, 1).getTime()));
      return [start, end];
    }
  },
  {
    text: '去年',
    value: () => {
      const end = new Date();
      const start = new Date();
      let currentDate = new Date();
      let currentYear = currentDate.getFullYear() - 1;
      start.setTime(new Date(currentYear, 0, 1).getTime());
      end.setTime(new Date(currentYear + 1, 0, 1).getTime() - 1);
      return [start, end];
    }
  }
];
