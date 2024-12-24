import sys, os, json, redis, math, time
from multiprocessing import Pool
#/usr/bin/python3 /home/www/2water.com/python/ecgs_ct.py /home/www/2water.com/source/pics/20210106113723759491.json sxqx_ct_v1 80 18 /usr/local/PaddleOCR 127.0.0.1#6379#5 /usr/bin/python3
start = time.time()#开始计时
try:
    jsonfile    = sys.argv[1]   #json文件的绝对路径
    modeldir    = sys.argv[2]   #识别引擎模型所在目录，在根目录/python/models下
except Exception as e:
    print("Usage: python3 xxxxx.py {jsonfile} {modeldir} {bkey}")
    exit(-1)
try:
    matching    = int(sys.argv[3])  #识别阈值
except Exception as e:
    matching    = 80
try:
    proces      = int(sys.argv[4])
except Exception as e:
    proces      = 4 #运行引擎的进程数
try:
    ocrdir      = sys.argv[5]
except Exception as e:
    ocrdir      = 4 #PaddleOCR路径
try:
    redis_server= sys.argv[6]
except Exception as e:
    redis_server= '127.0.0.1#6379#1' #redis服务器
try:
    pyf         = sys.argv[7]
except Exception as e:
    pyf         = '/usr/bin/python3' #python路径

with open(jsonfile, 'r', encoding='utf-8') as j:
    data = json.load(j)
if len(data) < 1:
    print('输入数据为空')
    exit()

ps = []
for order in data['orders']:
    if len(order['photos']) < 1:continue
    for photo in order['photos']:
        ####
        if order['gdtype'] == 0:
            if not (photo['ycode'] in [3,5,6,7,8,10,19,20,21,23,25,27]):continue;
        else:
            if order['gdtype'] == 2:
                if not (photo['ycode'] in [28,29,30,31,32,33,34]):continue;
        ####
        ydate = order['etime']
        pid = photo['id']
        imgfile = ydate[:4] + ydate[5:7] + '/' + str(order['id']) + '/' + photo['filename'] + '.' + photo['extension'];
        ps.append([pid, str(photo['ycode']) + '#' + imgfile]);
    #break
s = 100
c = len(ps)
z = math.ceil(c/s)

rdss = redis_server.split('#', 2)
Rds = redis.Redis(host=rdss[0], port=rdss[1], db=rdss[2], decode_responses=True)

for i in range(z):
    k = 'ps' + str(i)
    Rds.delete(k)
    for a in range(s):
        b = i * s + a
        Rds.hset(k, ps[b][0], ps[b][1])
        if b == c - 1:break
#print('照片总数：' + str(c))

PYPATH = os.path.abspath(os.path.dirname(__file__)).replace('\\', '/')

def mtd(pid):
    for i in range(z):
        if i % proces == pid: y = os.system(pyf + ' ' + PYPATH + '/mts.py ' + jsonfile + ' ' + modeldir + ' ' + str(matching) + ' ' + str(proces) + ' ' + ocrdir + ' ' + redis_server + ' ' + str(i));

if __name__ == '__main__':
    p = Pool(processes=proces)   #使用线程池建立N个子线程
    for i in range(proces):
        p.apply_async(mtd, args=(i,))
    p.close()
    p.join()
    end=time.time()
    print ("ok. Model:"+ modeldir +", Process:"+ str(proces) +", matching:"+ str(matching) +", Photos:"+ str(c) +", Time All:" + '{:.3f}s'.format(end-start) +", Time Average:" + str((end-start)/c))