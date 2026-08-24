FROM ubuntu:latest
LABEL authors="diazh"

ENTRYPOINT ["top", "-b"]
