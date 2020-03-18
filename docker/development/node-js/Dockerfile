FROM node:12.16.1-alpine

ARG USER_ID
ARG GROUP_ID

RUN apk update && apk add yarn \
    && yarn global add @vue/cli

# Установк bash
RUN apk add bash coreutils shadow

RUN usermod -u $USER_ID node

USER node

WORKDIR /frontend