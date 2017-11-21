#DROP TABLE users;
CREATE TABLE users (
  id INT(11) NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

#DROP TABLE photos;
CREATE TABLE photos (
  id INT(11) NOT NULL AUTO_INCREMENT,
  uid INT(11) NOT NULL,
  filename VARCHAR(128) NOT NULL,
  caption text NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (uid) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO photos(filename, caption, uid) VALUES
  ('test1.jpg', 'This is a test image.', 1),
  ('test2.png', 'This is another test image.', 1),
  ('test3.png', 'This is also a test image.', 2);