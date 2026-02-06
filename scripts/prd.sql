-- ============================================
-- Portal de Compras - CESAN
-- Script de criação da tabela FAVORITO_LICITACAO
-- ============================================

USE [PortalCompras];
GO

-- Criar tabela de favoritos
IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[FAVORITO_LICITACAO]') AND type in (N'U'))
BEGIN
    CREATE TABLE [dbo].[FAVORITO_LICITACAO] (
        [ID_FAVORITO]       INT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        [ID_ADM]            INT NOT NULL,
        [ID_LICITACAO]      INT NOT NULL,
        [DT_FAVORITO]       DATETIME NOT NULL DEFAULT GETDATE(),
        [DT_EXC_FAVORITO]   DATETIME NULL
    );

    -- Índice único para evitar duplicatas ativas (um favorito por usuário/licitação)
    CREATE UNIQUE INDEX [IX_FAVORITO_USUARIO_LICITACAO] 
        ON [dbo].[FAVORITO_LICITACAO] ([ID_ADM], [ID_LICITACAO]) 
        WHERE [DT_EXC_FAVORITO] IS NULL;

    -- Índice para consultas por usuário
    CREATE INDEX [IX_FAVORITO_USUARIO] 
        ON [dbo].[FAVORITO_LICITACAO] ([ID_ADM], [DT_EXC_FAVORITO]);

    PRINT 'Tabela FAVORITO_LICITACAO criada com sucesso.';
END
ELSE
BEGIN
    PRINT 'Tabela FAVORITO_LICITACAO já existe.';
END
GO