FROM python:3.10

RUN mkdir /app
WORKDIR /app

RUN pip install --upgrade pip

COPY requirements.txt /app

RUN pip install -r requirements.txt

COPY . /app