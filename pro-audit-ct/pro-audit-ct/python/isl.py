from multiprocessing import Pool
import cv2, os, math, time, pymysql, redis
import numpy as np

start = time.time()

Rds = redis.Redis(host='127.0.0.1', port=6379, db=11, decode_responses=True)

conn = pymysql.connect(host='127.0.0.1', user='root', password='Water~12123', database='2water', charset='utf8')
cur=conn.cursor();
cur.execute("SELECT id, concat(replace(left((select etime from orders where id = photos.oid), 7), '-', ''), '/', oid, '/', filename, '.', extension) path FROM `photos` where oid in(select id from orders where etime>=20210510 and etime <= 20210521 and status = 1) and status = 1");
res = cur.fetchall();

def lines(img):
    r = cv2.HoughLinesP(img, 1, np.pi/180, 20, np.array([]), 10, 1)
    if not (r is None):return len(r)
    return 0

def isl(row):
    #ls = [];
    img = cv2.imread('../source/pics/' + row[1], 1);
    y, x = img.shape[:2];
    y = int(y / 1.2)
    x = int(x / 1.2)
    img = cv2.resize(img, (x, y))
    img = cv2.filter2D(img, -1, kernel=np.array([[0, -1, 0], [-1, 5, -1], [0, -1, 0]], np.float32))#锐化
    img = cv2.Canny(img, 10, 10, apertureSize = 3)
    #print(x, y)
    s = 0
    for y1 in range(math.floor(y / 300)):
        y1 *= 300
        y2 = y1 + 300
        for x1 in range(math.floor(x / 300)):
            x1 *= 300
            x2 = x1 + 300
            l = lines(img[y1:y2, x1:x2])
            #print(l)
            if l >= 380:return Rds.sadd('ps', row[0])
            if l >= 260:s += 1;
            if s == 3:return Rds.sadd('ps', row[0])
            #if l > 0:ls.append(l)
    #return sorted(ls, reverse = True)
    return False

if __name__ == '__main__':
    p = Pool(processes=30)   #使用线程池建立4个子线程
    for row in res:
        #id = str(row[0]);
        #h = ish('../source/pics/' + row[1]);
        p.apply_async(isl, args=(row,))
            #break
        #break
    p.close()
    p.join()
    end=time.time()
    ps = len(res)
    print ("Photos:"+ str(ps) +", Time All:" + '{:.3f}s'.format(end-start) +", Time Average:" + str((end-start)/ps))
    rs = Rds.smembers('ps')
    for id in rs:
        cur.execute('insert ignore into `likes_210629` set matching = 99.99, pid2 = 0, pid = ' + str(id))
    Rds.delete('ps')
    cur.close()
    conn.commit()
    conn.close()
    print('okey')

'''

rs = Rds.smembers('ps');
for id in rs:
    print(id)
    cur.execute('insert ignore into `likes_210629` set matching = 99.99, pid2 = 0, pid = ' + str(id))
conn.commit()
cur.close()
conn.close()

for row in res:
    id = str(row[0]);
    h = ish('../source/pics/' + row[1]);
    print(id + ':' + str(h))
    if h: cur.execute('insert ignore into `likes` set matching = 99, pid2 = 0, pid = ' + id)

cur.close()
conn.commit()
conn.close()
print('okey')
'''
