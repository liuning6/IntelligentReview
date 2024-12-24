import tarfile, sys
try:
    tarf    = sys.argv[1]
    topath    = sys.argv[2]
except Exception as e:
    print("Usage: python3 untar.py {tarfile} {topath}")
    exit(-1)
tar = tarfile.open(tarf)
tar.extractall(path = topath)
print('success')
exit()