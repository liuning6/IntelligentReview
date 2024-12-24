# -*- coding: UTF-8 -*-
import sys, cv2, os, json, redis#, time
import numpy as np
from paddleocr import PaddleOCR
import tensorflow.compat.v1 as tf
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
os.environ["CUDA_VISIBLE_DEVICES"]="-1"
#/usr/bin/python3 /home/www/2water.com/python/mts.py /home/www/2water.com/source/pics/20210106113723759491.json sxqx_ct_v1 80 18 /usr/local/PaddleOCR 127.0.0.1#6379#5 1
#start = time.time()#开始计时
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
    redis_server= sys.argv[6]
except Exception as e:
    redis_server= '127.0.0.1#6379#1' #redis服务器
try:
    jid         = int(sys.argv[7])
except Exception as e:
    jid         = 0 #列队号

data = []
APP_PATH = os.path.abspath(os.path.dirname(os.path.dirname(__file__))).replace('\\', '/')
photo_dir = APP_PATH + '/source/pics/'
modeldir = APP_PATH + '/python/models/' + modeldir

rdss = redis_server.split('#', 2)
Rds = redis.Redis(host=rdss[0], port=rdss[1], db=rdss[2], decode_responses=True)
ocr = PaddleOCR(use_space_char = False,use_angle_cls=False,use_gpu=False,cls_batch_num=1,rec_batch_num=1,max_text_length=1,det=False,use_zero_copy_run=False,cls=False,rec=False).ocr

labels = []
proto_as_ascii_lines = tf.gfile.GFile(modeldir + '/labels').readlines()
for l in proto_as_ascii_lines:
    labels.append(l.rstrip())

ks = {5:['xiaoqumentou', 'xiaoqushiyitu', 'menpai'], 6:['hanyangliang', 'hanyangliang2', 'hanyangliang3'], 7:['pensaxiaoduji'], 10:['zonglv', 'zonglv2', 'zonglv3', 'zonglv4', 'hunzhuoduzonglv', 'zonglv5'], 19:['shuixiangsuo'], 20:['hunzhuodu', 'hunzhuodu2', 'hunzhuodu3', 'hunzhuoduzonglv', 'hunzhuodu5', 'hunzhuodu6', 'hunzhuodu7'], 21:['xiaoduji'], 23:['xiaodujikuaijian'], 25:['banqianjiaoyu'], 27:['menpai'], 28:['gongzuozheng'], 29:['menpai'], 30:['bengfangneihuanjing'], 31:['bengfangneihuanjing', 'shuibengjizuquanjing'], 32:['diankongguiwaibu'], 33:['diankongguineibu'], 34:['bengfangbaoyangjilu']}
fs = {'banqianjiaoyu':3101, 'hanyangliang':2201, 'hanyangliang2':2202, 'hanyangliang3':2203, 'hunzhuodu':2401, 'hunzhuodu2':2402, 'hunzhuodu3':2403, 'hunzhuoduzonglv':2450, 'hunzhuodu5':2405, 'hunzhuodu6':2406, 'hunzhuodu7':2407, 'menpai':2101, 'pensaxiaoduji':2301, 'shuixiangsuo':2601, 'xiaodujikuaijian':1202, 'xiaoduji':1201, 'xiaoqumentou':1101, 'xiaoqushiyitu':1102, 'zonglv':2501, 'zonglv2':2502, 'zonglv3':2503, 'zonglv4':2504, 'zonglv5':2505, 'tingshuitongzhi':4201, 'anquanzerenshu':4101, 'gongzuozheng':5101, 'bengfangneihuanjing':5201, 'shuibengjizuquanjing':5301, 'diankongguiwaibu':5401, 'diankongguineibu':5451, 'bengfangbaoyangjilu':4301}

class Mts:
    def __init__(self, modeldir):
        graph = tf.Graph()
        graph_def = tf.GraphDef()
        with open(modeldir + '/graph', "rb") as f:
            graph_def.ParseFromString(f.read())
        with graph.as_default():
            tf.import_graph_def(graph_def)
        self.input_operation = graph.get_operation_by_name("import/input").outputs[0];
        self.output_operation = graph.get_operation_by_name("import/final_result").outputs[0];
        self.graph=graph
        self.float32 = tf.float32
        self.decode_jpeg = tf.image.decode_jpeg
        self.read_file = tf.read_file
        self.cast = tf.cast
        self.expand_dims = tf.expand_dims
        self.resize_bilinear = tf.image.resize_bilinear
        self.divide = tf.divide
        self.subtract = tf.subtract
        self.s0 = tf.Session()
        self.s1 = tf.Session(graph=self.graph)
    def __call__(self, type, img):
        image_reader = self.decode_jpeg(self.read_file(img, "file_reader"), channels = 3, name='jpeg_reader')
        float_caster = self.cast(image_reader, self.float32)
        dims_expander = self.expand_dims(float_caster, 0);
        resized = self.resize_bilinear(dims_expander, [224, 224])
        normalized = self.divide(self.subtract(resized, [128]), [128])
        t = self.s0.run(normalized)
        results = self.s1.run(self.output_operation, {self.input_operation: t})
        results = np.squeeze(results)
        top_k = results.argsort()[-3:][::-1]
        if len(top_k) < 1:
            return ''
        for i in top_k:
            label = labels[i]
            score = results[i] * 100
            if score > matching:
                for f in ks[type]:
                    if f == label:return str(fs[label]) + ',' + str('%.6f' % score) + ',1';
        top = top_k[0];
        return str(fs[labels[top]]) + ',' + str('%.6f' % (results[top] * 100)) + ',2';

mt = Mts(modeldir)

def ftext(type, result):
    WenZi = ''
    for line in result:
        WenZi = WenZi + line[1][0]
    if WenZi == '':return ''
    WenZi = WenZi.replace('（', '').replace('）', '').replace('(', '').replace(')', '').replace('〈', '').replace('〉', '').replace("\n", '')[0:100]
    if type == 3:
        if '水箱池清' in WenZi:return 'tingshuitongzhi'
        if '箱池清洗' in WenZi:return 'tingshuitongzhi'
        if '池清洗告' in WenZi:return 'tingshuitongzhi'
        if '清洗告示' in WenZi:return 'tingshuitongzhi'
    elif type == 8:
        if '技术' in WenZi:return 'anquanzerenshu'
        if '术交' in WenZi:return 'anquanzerenshu'
        if '交底' in WenZi:return 'anquanzerenshu'
    else:
        if '泵房设备' in WenZi:return 'bengfangbaoyangjilu'
        if '设备维保' in WenZi:return 'bengfangbaoyangjilu'
        if '季度记录' in WenZi:return 'bengfangbaoyangjilu'
        if '维保项目' in WenZi:return 'bengfangbaoyangjilu'
    return ''

def ot(type, img):
    img = cv2.imread(img)
    y, x = img.shape[:2]
    b = 4
    x1 = int(x / b)
    y1 = int(y / b)
    img1 = cv2.resize(img, (y1, x1))
    img1 = img1[:int(y1 / 2), :x1]
    result = ocr(img1)
    label = ftext(type, result)
    if label == '':
        result = ocr(img)
        label = ftext(type, result)
    if label == '':return ''
    return str(fs[label]) + ',99.999999,1'

if __name__ == '__main__':
    k = 'ps'+ str(jid)
    ps = Rds.hgetall(k)
    c = len(ps)
    for v in ps:
        p = ps[v].split('#');
        type = int(p[0]);
        photo = photo_dir + p[1];
        rs = ''
        if os.path.exists(photo):
            if type in [3, 8, 34]:
                rs = ot(type, photo);
            else:
                rs = mt(type, photo);
        if rs != '':Rds.hset("photos", v, rs)
    Rds.delete(k)
    #end=time.time()
    #print ("ok. Model:"+ modeldir +", Process:"+ str(proces) +", matching:"+ str(matching) +", Photos:"+ str(c) +", Time All:" + '{:.3f}s'.format(end-start) +", Time Average:" + str((end-start)/c) + ', jid:' + str(jid))