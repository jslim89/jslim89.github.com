---
title: docker-compose for Elasticsearch & Kibana
date: 2019-07-11 19:20:29
tags:
- docker
- elasticsearch
- kibana
---

Create a file **docker-compose.yml** with the following content:

```yml
version: '3'

services:
  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.2.0
    container_name: elasticsearch_local
    environment:
      - cluster.name=docker-cluster
      - bootstrap.memory_lock=true
      - discovery.type=single-node
      - http.cors.enabled=true
      - http.cors.allow-credentials=true
      - http.cors.allow-headers=X-Requested-With,X-Auth-Token,Content-Type,Content-Length,Authorization
      - http.cors.allow-origin=/https?:\/\/localhost(:[0-9]+)?/
      - "ES_JAVA_OPTS=-Xms512m -Xmx512m"
    ulimits:
      memlock:
        soft: -1
        hard: -1
    volumes:
      - esdata1:/usr/share/elasticsearch/data
    ports:
      - 9200:9200
    networks:
      - esnet
  kibana:
    image: docker.elastic.co/kibana/kibana:7.2.0
    container_name: kibana_1
    environment:
      - "SERVER_NAME=kibana"
      - "ELASTICSEARCH_HOSTS=http://elasticsearch:9200"
    ports:
      - 5601:5601
    networks:
      - esnet
    restart: "unless-stopped"

volumes:
  esdata1:
    driver: local

networks:
  esnet:
```

Then run

```
$ docker-compose up -d
```

_NOTE: must specify `networks`, otherwise kibana won't connect with elasticsearch_

## References:

- [Elasticsearch + Kibana using Docker Compose](https://alysivji.github.io/elasticsearch-kibana-with-docker-compose.html)
- [Running Kibana on Docker](https://www.elastic.co/guide/en/kibana/current/docker.html)
