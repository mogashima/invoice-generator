# 請求書管理アプリ

Slimフレームワークを利用してPHPで実装しています。
フロントエンドはtwigを利用しています。

## 構築手順

### composerで依存ライブラリをインストール

```bash
composer install
```

### envファイルを作成

.env.exampleファイルをコピーして内容を変更します。

### データベース準備

マイグレーションでデータベースにテーブルを作成できます。

```bash
vendor/bin/phinx migrate
```

ロールバックはを行う際は次のコマンドを利用します。

```bash
vendor/bin/phinx rollback
```
