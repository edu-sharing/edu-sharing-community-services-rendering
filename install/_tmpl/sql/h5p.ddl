CREATE TABLE h5p_libraries_languages (
    library_id integer NOT NULL,
    translation text NOT NULL,
    language_code varchar(31) NOT NULL);

CREATE TABLE h5p_libraries (
    id SERIAL,
    fullscreen integer NOT NULL,
    runnable integer NOT NULL,
    name varchar(127) NOT NULL,
    title varchar(255) NOT NULL,
    has_icon integer NOT NULL DEFAULT '0' ,
    restricted integer NOT NULL DEFAULT '0' ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    major_version integer NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00' ,
    embed_types varchar(255) NOT NULL,
    minor_version integer NOT NULL,
    patch_version integer NOT NULL,
    drop_library_css text NULL,
    preloaded_js text NULL,
    preloaded_css text NULL,
    tutorial_url varchar(1023) NOT NULL,
    semantics text NOT NULL,
    PRIMARY KEY (id));

CREATE INDEX idx_h5p_libraries_name
    ON h5p_libraries (name, title);

CREATE TABLE h5p_libraries_libraries (
    library_id integer NOT NULL,
    required_library_id integer NOT NULL,
    dependency_type varchar(31) NOT NULL);

CREATE TABLE h5p_contents (
    id SERIAL,
    library_id integer NOT NULL,
    user_id integer NOT NULL,
    license varchar(7) NULL,
    parameters text NOT NULL,
    title varchar(255) NOT NULL,
    created_at TIMESTAMP NOT NULL  DEFAULT '0000-00-00 00:00:00' ,
    author varchar(127) NULL,
    updated_at TIMESTAMP NOT NULL  DEFAULT '0000-00-00 00:00:00' ,
    slug varchar(127) NOT NULL,
    keywords text NULL,
    disable integer NOT NULL  DEFAULT '0' ,
    content_type varchar(127) NULL,
    embed_type varchar(127) NOT NULL,
    filtered text NOT NULL,
    description text NULL,
    PRIMARY KEY (id));

CREATE TABLE h5p_contents_libraries (
    id SERIAL,
    library_id integer NOT NULL,
    drop_css integer NOT NULL,
    content_id integer NOT NULL,
    weight integer NOT NULL  DEFAULT '0' ,
    dependency_type varchar(31) NOT NULL,
    PRIMARY KEY (id));

CREATE TABLE h5p_libraries_hub_cache (
    id SERIAL,
    machine_name varchar(127) NOT NULL,
    updated_at integer NOT NULL,
    keywords text NULL,
    owner varchar(511) NULL,
    screenshots text NULL,
    tutorial varchar(511) NULL,
    title varchar(255) NOT NULL,
    minor_version integer NOT NULL,
    popularity integer NOT NULL,
    description text NOT NULL,
    h5p_minor_version integer NULL,
    h5p_major_version integer NULL,
    categories text NULL,
    icon varchar(511) NOT NULL,
    license text NULL,
    created_at TIMESTAMP NOT NULL,
    major_version integer NOT NULL,
    summary text NOT NULL,
    patch_version integer NOT NULL,
    is_recommended integer NOT NULL,
    example varchar(511) NOT NULL,
    PRIMARY KEY (id));