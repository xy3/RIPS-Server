import sys, time, os, hashlib
import mmap

# CHUNKSIZE = 1024*8
CHUNKSIZE = 8192

def encode(infile, outfile, chunksize=CHUNKSIZE):
	if os.path.exists(outfile):
		os.remove(outfile)

	size = os.path.getsize(infile)
	print(size)
	print(chunksize)
	
	with open(outfile, "ab+") as of:
		with open(infile, "rb") as f:
			vid = mmap.mmap(f.fileno(), 0, access=mmap.ACCESS_READ)
			p_from = size - chunksize
			p_to = size
			
			print(f"{p_from} -> {p_to}")
			while p_from > 0:
				of.write(vid[p_from:p_to])
				p_from -= chunksize
				p_to -= chunksize
			
			print(f"0 -> {p_to}")
			of.write(vid[0:p_to])
			
			# of.write(mm[::-1])

			# of.write(f.read()[::-1])
			# try:
			# 	chunk = f.read(chunksize)
			# 	while chunk:
			# 		of.write(bytes(chunk)[::2])
			# 		of.write(bytes(chunk)[1::2])
			# 		chunk = f.read(chunksize)
			# except Exception as e:
			# 	raise e
			# 	sys.exit(1)
			# except KeyboardInterrupt:
			# 	print("Bye")
			# 	sys.exit(1)
			return 1
	return 0


if __name__ == '__main__':
	file = sys.argv[1].strip()
	location = ''
	filename = file
	
	if '/' in file:
		location, filename = file.rsplit("/",1)
	
	md5 = hashlib.md5(filename.encode()).hexdigest()
	outfile = f"{location}/{md5}"
	
	if encode(file, outfile):
		print(f"{file} => {outfile}")
	else:
		print("error")

