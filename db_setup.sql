DROP TABLE IF EXISTS auth;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS admins;

CREATE TABLE posts (
    id          INT             NOT NULL    AUTO_INCREMENT,
    ctime       TIMESTAMP       NOT NULL    DEFAULT         (CURRENT_TIMESTAMP),
    title       VARCHAR(255)    NOT NULL,
    content     TEXT            NOT NULL,

    PRIMARY KEY (id)
);

CREATE TABLE admins (
    id          INT             NOT NULL    AUTO_INCREMENT,
    user        VARCHAR(25)     NOT NULL    UNIQUE,
    pswd        VARCHAR(64)     NOT NULL,

    PRIMARY KEY (id)
);

CREATE TABLE auth (
    token       CHAR(64)        NOT NULL,
    user        INT             NOT NULL,
    ctime       TIMESTAMP       NOT NULL    DEFAULT         (CURRENT_TIMESTAMP),

    PRIMARY KEY (token, user),
    FOREIGN KEY (user)          REFERENCES  admins          (id)
);