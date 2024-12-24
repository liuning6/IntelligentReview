from multiprocessing import Pool
import cv2, os, time, pymysql, redis

start = time.time()

Rds = redis.Redis(host='127.0.0.1', port=6379, db=11, decode_responses=True)

conn = pymysql.connect(host='127.0.0.1', user='root', password='Water~12123', database='2water', charset='utf8')
cur=conn.cursor();
cur.execute("SELECT id, concat(replace(left((select etime from orders where id = photos.oid), 7), '-', ''), '/', oid, '/', filename, '.', extension) path FROM `photos` where oid in(select id from orders where etime>=20210510 and etime <= 20210521 and status = 1) and status = 1");
res = cur.fetchall();

def ish(row):
    image=cv2.imread('../source/pics/' + row[1], 1) #读取图片 image_name应该是变量
    img = cv2.resize(image, (80, 100))
    img = cv2.medianBlur(img, 5) #中值滤波，去除黑色边际中可能含有的噪声干扰
    b=cv2.threshold(img, 8, 255, cv2.THRESH_BINARY)          #调整裁剪效果
    binary_image=b[1]               #二值图--具有三通道
    binary_image=cv2.cvtColor(binary_image, cv2.COLOR_BGR2GRAY)
    ###cv2.imwrite('111.jpg', binary_image)
    ###edges_x=[]
    edges_y=[]
    for i in range(100):
        for j in range(80):
            if binary_image[i][j]==255:
                edges_y.append(i)
                ###edges_x.append(j)

    ###left=min(edges_x)               #左边界
    ###right=max(edges_x)              #右边界
    ###width=right-left                #宽度
    bottom = max(edges_y)             #底部
    top = min(edges_y)                #顶部
    ###height=top-bottom               #高度
    #print(top, bottom)
    ###pre1_picture=image[left:left+width,bottom:bottom+height]        #图片截取
    ###return pre1_picture                                             #返回图片数据
    ###cv2.imwrite('222.jpg', img[top:bottom, :80])
    t = 100 - bottom
    l = len(edges_y)
    #print(top, t, l)
    if l < 3500: return False
    if (top >= 15 or t >= 15 or (top >= 10 and t >= 5) or (top >= 5 and t >= 10)): Rds.sadd('ps', row[0])#return True
    return False


if __name__ == '__main__':
    p = Pool(processes=30)   #使用线程池建立4个子线程
    for row in res:
        #id = str(row[0]);
        #h = ish('../source/pics/' + row[1]);
        p.apply_async(ish, args=(row,))
            #break
        #break
    p.close()
    p.join()
    end=time.time()
    ps = len(res)
    print ("Photos:"+ str(ps) +", Time All:" + '{:.3f}s'.format(end-start) +", Time Average:" + str((end-start)/ps))
    rs = Rds.smembers('ps')
    for id in rs:
        cur.execute('insert ignore into `likes_210629` set matching = 99, pid2 = 0, pid = ' + str(id))
    Rds.delete('ps')
    cur.close()
    conn.commit()
    conn.close()
    print('okey')

'''
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
