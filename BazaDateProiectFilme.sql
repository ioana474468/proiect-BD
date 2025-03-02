CREATE DATABASE BDFilme COLLATE Latin1_General_100_CI_AS_SC

CREATE TABLE Utilizatori
(
	UtilizatorID int identity(1,1) NOT NULL,
	Nume nvarchar(50) NOT NULL,
	Email nvarchar(50) NOT NULL,
	DataNasterii date,
	Tara nvarchar(25),
	Parola varbinary(32) NOT NULL,
	RolAdmin binary(1) DEFAULT 0,
	DataInregistrarii smalldatetime NOT NULL,
	CONSTRAINT PK_Utilizatori PRIMARY KEY (UtilizatorID),
	CONSTRAINT UNQ_Angajati_Nume UNIQUE (Nume),
	CONSTRAINT CHK_Utilizator_RolAdmin CHECK (RolAdmin=0 OR RolAdmin=1)
)

CREATE TABLE Actori
(
	ActorID int identity(1,1) NOT NULL,
	Nume nvarchar(50) NOT NULL,
	Prenume nvarchar(50) NOT NULL,
	DataNasterii date,
	Nationalitate nvarchar(20),
	Biografie nvarchar(2000),
	Poza nvarchar(255), -- link poza
	CONSTRAINT PK_Actori PRIMARY KEY (ActorID)
)

CREATE TABLE CategoriiFilme
(
	CategorieFilmID int identity(1,1) NOT NULL,
	Nume nvarchar(50) NOT NULL,
	DescriereCategorie nvarchar(500),
	CONSTRAINT PK_CatergoriiFilme PRIMARY KEY (CategorieFilmID)
)

CREATE TABLE Regizori
(
	RegizorID int identity(1,1) NOT NULL,
	Nume nvarchar(50) NOT NULL,
	Prenume nvarchar(50) NOT NULL,
	DataNasterii date,
	Nationalitate nvarchar(20),
	Biografie nvarchar(2000),
	Poza nvarchar(255), -- link poza
	CONSTRAINT PK_Regizori PRIMARY KEY (RegizorID)
)

CREATE TABLE Filme
(
	FilmID int identity(1,1) NOT NULL,
	RegizorID int NOT NULL,
	CategorieFilmID int NOT NULL,
	Titlu nvarchar(50) NOT NULL,
	DataLansarii date NOT NULL,
	Durata int NOT NULL,
	DescriereFilm nvarchar(2000),
	Poster nvarchar(255), -- link poza
	PremiuOscar binary(1) DEFAULT 0,
	CONSTRAINT PK_Filme PRIMARY KEY (FilmID),
	CONSTRAINT CHK_Filme_PremiuOscar CHECK (PremiuOscar=0 OR PremiuOscar=1),
	CONSTRAINT FK_Filme_Regizori FOREIGN KEY (RegizorID) REFERENCES Regizori(RegizorID),
	CONSTRAINT FK_Filme_CategoriiFilme FOREIGN KEY (CategorieFilmID) REFERENCES CategoriiFilme(CategorieFilmID)
)

CREATE TABLE FilmeActori
(
	FilmID int NOT NULL,
	ActorID int NOT NULL,
	Personaj nvarchar(50) NOT NULL,
	TipRol char(10) NOT NULL, --principal, secundar
	PremiuOscar binary(1) DEFAULT 0,
	CONSTRAINT PK_FilmeActori PRIMARY KEY (FilmID,ActorID),
	CONSTRAINT CHK_FilmeActori_PremiuOscar CHECK (PremiuOscar=0 OR PremiuOscar=1),
	CONSTRAINT FK_FilmeActori_Filme FOREIGN KEY (FilmID) REFERENCES Filme(FilmID),
	CONSTRAINT FK_FilmeActori_Actori FOREIGN KEY (ActorID) REFERENCES Actori(ActorID)
)

CREATE TABLE UtilizatoriFilme
(
	UtilizatorID int NOT NULL,
	FilmID int NOT NULL,
	Favorit binary(1) DEFAULT 0,
	DataAdaugarii date NOT NULL,
	StatusVizionare binary(1) DEFAULT 0,
	CONSTRAINT PK_UtilizatoriFilme PRIMARY KEY (UtilizatorID,FilmID),
	CONSTRAINT CHK_UtilizatoriFilme_Favorit CHECK (Favorit=0 OR Favorit=1),
	CONSTRAINT CHK_UtilizatoriFilme_StatusVizionare CHECK (StatusVizionare=0 OR StatusVizionare=1),
	CONSTRAINT FK_UtilizatoriFilme_Utilizatori FOREIGN KEY (UtilizatorID) REFERENCES Utilizatori(UtilizatorID),
	CONSTRAINT FK_UtilizatoriFilme_Filme FOREIGN KEY (FilmID) REFERENCES Filme(FilmID)
)

CREATE TABLE Reviewuri
(
	ReviewID int identity(1,1) NOT NULL,
	UtilizatorID int NOT NULL,
	FilmID int NOT NULL,
	ContinutReview nvarchar(2000) NOT NULL,
	Rating DECIMAL(3,1) NOT NULL,
	DataPostarii smalldatetime NOT NULL,
	CONSTRAINT PK_Reviuwuri PRIMARY KEY (ReviewID),
	CONSTRAINT FK_Reviewuri_Utilizatori FOREIGN KEY (UtilizatorID) REFERENCES Utilizatori(UtilizatorID),
	CONSTRAINT FK_Reviewuri_Filme FOREIGN KEY (FilmID) REFERENCES Filme(FilmID)
)

CREATE TABLE Comentarii
(
	ComentariuID int identity(1,1) NOT NULL,
	UtilizatorID int NOT NULL,
	ReviewID int NOT NULL,
	ContinutComentariu nvarchar(2000) NOT NULL,
	DataPostarii smalldatetime NOT NULL,
	CONSTRAINT PK_Comentarii PRIMARY KEY (ComentariuID),
	CONSTRAINT FK_Comentarii_Utilizatori FOREIGN KEY (UtilizatorID) REFERENCES Utilizatori(UtilizatorID),
	CONSTRAINT FK_Comentarii_Reviewuri FOREIGN KEY (ReviewID) REFERENCES Reviewuri(ReviewID)
)
