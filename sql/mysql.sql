CREATE TABLE marquee (
  marquee_marqueeid       INT(8)       NOT NULL AUTO_INCREMENT,
  marquee_uid             MEDIUMINT(8) NOT NULL DEFAULT '0',
  marquee_direction       SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_scrollamount    INT(11)      NOT NULL DEFAULT '0',
  marquee_behaviour       SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_bgcolor         VARCHAR(7)   NOT NULL DEFAULT '',
  marquee_align           SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_height          SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_width           VARCHAR(4)   NOT NULL DEFAULT '',
  marquee_hspace          SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_scrolldelay     SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_stoponmouseover SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_loop            SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_vspace          SMALLINT(6)  NOT NULL DEFAULT '0',
  marquee_content         TEXT         NOT NULL,
  marquee_source          VARCHAR(255) NOT NULL DEFAULT 'fixed',
  PRIMARY KEY (marquee_marqueeid)
)
  ENGINE = MyISAM
  AUTO_INCREMENT = 1;
