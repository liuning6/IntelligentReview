import os, sys, copy, cv2
import numpy as np
try:
    Pdir = sys.argv[5]
except Exception as e:
    Pdir = '/usr/local/PaddleOCR'

sys.path.append(Pdir)
sys.path.append(Pdir + '/tools/infer')

import tools.infer.predict_det as predict_det
import tools.infer.predict_rec as predict_rec
import tools.infer.predict_cls as predict_cls


class TextSystem(object):
    def __init__(self, args):
        self.text_detector = predict_det.TextDetector(args)
        self.text_recognizer = predict_rec.TextRecognizer(args)
        self.drop_score = args.drop_score

    def get_rotate_crop_image(self, img, points):
        '''
        img_height, img_width = img.shape[0:2]
        left = int(np.min(points[:, 0]))
        right = int(np.max(points[:, 0]))
        top = int(np.min(points[:, 1]))
        bottom = int(np.max(points[:, 1]))
        img_crop = img[top:bottom, left:right, :].copy()
        points[:, 0] = points[:, 0] - left
        points[:, 1] = points[:, 1] - top
        '''
        img_crop_width = int(
            max(
                np.linalg.norm(points[0] - points[1]),
                np.linalg.norm(points[2] - points[3])))
        img_crop_height = int(
            max(
                np.linalg.norm(points[0] - points[3]),
                np.linalg.norm(points[1] - points[2])))
        pts_std = np.float32([[0, 0], [img_crop_width, 0],
                              [img_crop_width, img_crop_height],
                              [0, img_crop_height]])
        M = cv2.getPerspectiveTransform(points, pts_std)
        dst_img = cv2.warpPerspective(
            img,
            M, (img_crop_width, img_crop_height),
            borderMode=cv2.BORDER_REPLICATE,
            flags=cv2.INTER_CUBIC)
        dst_img_height, dst_img_width = dst_img.shape[0:2]
        if dst_img_height * 1.0 / dst_img_width >= 1.5:
            dst_img = np.rot90(dst_img)
        return dst_img

    def __call__(self, img):
        ori_im = img.copy()
        dt_boxes, elapse = self.text_detector(img)
        if dt_boxes is None:
            return None, None
        img_crop_list = []

        dt_boxes = sorted_boxes(dt_boxes)

        for bno in range(len(dt_boxes)):
            tmp_box = copy.deepcopy(dt_boxes[bno])
            img_crop = self.get_rotate_crop_image(ori_im, tmp_box)
            img_crop_list.append(img_crop)
        rec_res, elapse = self.text_recognizer(img_crop_list)
        filter_rec_res = ''
        for rec_reuslt in rec_res:
            if rec_reuslt[1] >= self.drop_score:
                filter_rec_res = filter_rec_res + rec_reuslt[0]
        return filter_rec_res

def sorted_boxes(dt_boxes):
    """
    Sort text boxes in order from top to bottom, left to right
    args:
        dt_boxes(array):detected text boxes with shape [4, 2]
    return:
        sorted boxes(array) with shape [4, 2]
    """
    num_boxes = dt_boxes.shape[0]
    sorted_boxes = sorted(dt_boxes, key=lambda x: (x[0][1], x[0][0]))
    _boxes = list(sorted_boxes)

    for i in range(num_boxes - 1):
        if abs(_boxes[i + 1][0][1] - _boxes[i][0][1]) < 10 and \
                (_boxes[i + 1][0][0] < _boxes[i][0][0]):
            tmp = _boxes[i]
            _boxes[i] = _boxes[i + 1]
            _boxes[i + 1] = tmp
    return _boxes

class conf():
    def __init__(self):
        self.det_model_dir = Pdir + '/inference/ch_ppocr_mobile_v1.1_det_infer/'
        self.rec_model_dir = Pdir + '/inference/ch_ppocr_mobile_v1.1_rec_infer/'
        self.cls_model_dir = Pdir + '/inference/ch_ppocr_mobile_v1.1_cls_infer/'
        self.rec_char_dict_path = Pdir + '/ppocr/utils/ppocr_keys_v1.txt'
        self.vis_font_path = Pdir + '/doc/simfang.ttf'
        self.use_gpu=False
        self.use_space_char=False
        self.drop_score=0.3
        self.cls_batch_num=30
        self.cls_image_shape='3, 48, 192'
        self.cls_thresh=0.9
        self.det_algorithm='DB'
        self.det_db_box_thresh=0.5
        self.det_db_thresh=0.3
        self.det_db_unclip_ratio=1.6
        self.det_east_cover_thresh=0.1
        self.det_east_nms_thresh=0.2
        self.det_east_score_thresh=0.8
        self.det_max_side_len=960
        self.det_sast_nms_thresh=0.2
        self.det_sast_polygon=False
        self.det_sast_score_thresh=0.5
        self.enable_mkldnn=False
        self.gpu_mem=8000
        self.ir_optim=True
        self.label_list=['0', '180']
        self.max_text_length=25
        self.rec_algorithm='CRNN'
        self.rec_batch_num=6
        self.rec_char_type='ch'
        self.rec_image_shape='3, 32, 320'
        self.use_pdserving=False
        self.use_tensorrt=False
        self.use_zero_copy_run=False

def ocr():
    return TextSystem(conf())