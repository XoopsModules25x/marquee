CREATE TABLE marquee (
  marquee_marqueeid int(8) NOT NULL auto_increment,
  marquee_uid mediumint(8) NOT NULL default '0',
  marquee_direction smallint(6) NOT NULL default '0',
  marquee_scrollamount int(11) NOT NULL default '0',
  marquee_behaviour smallint(6) NOT NULL default '0',
  marquee_bgcolor varchar(7) NOT NULL default '',
  marquee_align smallint(6) NOT NULL default '0',
  marquee_height smallint(6) NOT NULL default '0',
  marquee_width varchar(4) NOT NULL default '',
  marquee_hspace smallint(6) NOT NULL default '0',
  marquee_scrolldelay smallint(6) NOT NULL default '0',
  marquee_stoponmouseover smallint(6) NOT NULL default '0',
  marquee_loop smallint(6) NOT NULL default '0',
  marquee_vspace smallint(6) NOT NULL default '0',
  marquee_content text NOT NULL,
  marquee_source varchar(255) NOT NULL default 'fixed',
  PRIMARY KEY  (marquee_marqueeid)
) ENGINE=MyISAM AUTO_INCREMENT=1 ;
