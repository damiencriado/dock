<h1 align="center">Dock 🐳</h1>
<p align="center">
<img src="https://github.com/damiencriado/dock/workflows/Build%20&%20Release/badge.svg" alt="Build & Release">
<img src="https://img.shields.io/github/v/release/damiencriado/dock" alt="Latest Stable Version">
<img src="https://img.shields.io/github/license/damiencriado/dock" alt="License">
</p>

## Introduction

Dock is a GUI tool made with CLI to manage Docker.

<p align="center"><img src="https://raw.githubusercontent.com/damiencriado/art/master/dock/screen1.png" width="650"></p>

## Install

### Option 1: Manually
Download latest release: https://github.com/damiencriado/dock/releases

Make dock executable:
```sh
chmod +x /usr/local/bin/dock
```

### Option 2: Automatically
Use this shell script to install `dock` to `/usr/local/bin/dock`
```sh
sudo mkdir -p /usr/local/bin && curl -L -o /usr/local/bin/dock $(curl -s https://api.github.com/repos/damiencriado/dock/releases/latest | grep "browser_" | cut -d\" -f4) && sudo chmod +x /usr/local/bin/dock
```

## Usage

```sh
$ dock
```

If `docker-compose.yml` is found in current dir, some `docker-compose` options will be available.

## Update

```sh
$ dock self-update
```
