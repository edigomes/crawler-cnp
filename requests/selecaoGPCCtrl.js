app.controller('SelecaoGPCCtrl', ['$scope', 'genericService','blockUI',
    function ($scope, genericService, blockUI) {

        $scope.gpcs = gpcs;
        $scope.filter = "";
        $scope.ultimaPesquisaRealizada = "";

        var initial = gpcs.map(function (x) { return { item: x } });

        $scope.segmentos = _.chain(gpcs)
            .groupBy(function(x) { return x.segmentId })
            .map(function(x) {
                return { descricao: x[0].segment, codigo: x[0].segmentId }
            })
            .value();
        $scope.segmentoFiltrado = "";
        $scope.pesquisaRealizada = false;
        $scope.resultadosEncontrados = false;
        $scope.nomeSegmentoFiltrado = '';

        $scope.searchResultTree = _(initial).groupBy("item.segmentId").map(mapSegment).value();

        $scope.searchGpc = function (ignoreRestrictions) {
            $('[data-botao-pesquisar]').attr('disabled', 'disabled');
            blockUI.start();
            setTimeout(function () {
                var searchTerm = $scope.filter;
                var caracteresSuficientesDigitados = searchTerm.length >= 3;
                var codigoSegmentoFiltrado = parseInt($scope.segmentoFiltrado);
                var mappedSearchResult;

                if (!ignoreRestrictions && (!caracteresSuficientesDigitados || !codigoSegmentoFiltrado)) {
                    mappedSearchResult = _(initial).groupBy("item.segmentId").map(mapSegment).value();

                    $scope.pesquisaRealizada = false;
                    $scope.resultadosEncontrados = false;
                    $scope.alert("Preencha pelo menos 3 caractéres e selecione um segmento de GPC antes de efetuar a pesquisa.");
                } else {
                    var gpcsFiltradosPorSegmento = $scope.gpcs;

                    if (!!codigoSegmentoFiltrado && codigoSegmentoFiltrado > 0)
                        gpcsFiltradosPorSegmento = _.filter(gpcsFiltradosPorSegmento, { 'segmentId': codigoSegmentoFiltrado });

                    if (gpcsFiltradosPorSegmento.length > 0) {
                        var searchEngine = configureSearchEngine(gpcsFiltradosPorSegmento);
                        var searchResult;

                        searchResult = (searchTerm.length == 0) ? searchEngine.search(gpcsFiltradosPorSegmento[0].segment) : searchEngine.search(searchTerm);

                        mappedSearchResult = _(searchResult).groupBy("item.segmentId").map(mapSegment).value();

                        $scope.pesquisaRealizada = true;
                        $scope.ultimaPesquisaRealizada = searchTerm;
                        $scope.resultadosEncontrados = !!mappedSearchResult && mappedSearchResult.length > 0;

                        $scope.searchResultTree = mappedSearchResult;
                    }

                }

                $('[data-botao-pesquisar]').removeAttr('disabled');
                blockUI.stop();
            }, 150);
        }
        
        $scope.selecionarSegmentoFrequente = function (codigoSegmento) {
            var segmentoJaSelecionado = $scope.segmentoFiltrado == codigoSegmento;

            if (segmentoJaSelecionado)
                codigoSegmento = "";

            $scope.segmentoFiltrado = codigoSegmento;
            $scope.searchGpc(true);
        }
        $scope.selecionarSugestaoDeSegmento = function (codigoSegmento) {
            $scope.segmentoFiltrado = codigoSegmento;
            $(window).scrollTop(0);
            $scope.searchGpc(true);
        }

        $scope.buscarNomeDoSegmento = function (codigoSegmento) {
            var segmentoEncontrado = _.find($scope.segmentos, { codigo: codigoSegmento });

            if (!!segmentoEncontrado) {

                $scope.nomeSegmentoFiltrado = segmentoEncontrado.descricao;
            }
            else
                $scope.nomeSegmentoFiltrado = '';

            return null;
        }

        $scope.teclaEnterPressionada = function (event) {
            var teclaEnterFoiPressionada = event.which === 13;

            if (teclaEnterFoiPressionada)
                $scope.searchGpc();
        }

        $scope.selecionarUltimosGpcs = function(selectedGpc) {
            $scope.selecionaGpc({
                segment: {
                    segmentId: selectedGpc.segmentId,
                    name: selectedGpc.segment,
                },
                family: {
                    familyId: selectedGpc.familyId,
                    name: selectedGpc.family,
                },
                "class": {
                    classId: selectedGpc.classId,
                    name: selectedGpc.class,
                },
                brick: {
                    brickId: selectedGpc.brickId,
                    name: selectedGpc.brick,
                }
            });
        }

        $scope.selecionaGpc = function(selectedGpc) {
            var element = document.getElementById(selectedGpc.brick.brickId);
            if (element)
                element.checked = true;
            $scope.$parent.gpcSelecionado = true;
            $scope.selectedGpc = selectedGpc;
        }

        $scope.selecionaGpcDaTabelaDeUltimosUtilizados = function (selectedGpc) {
            var formattedSelectedGpc = {
                segment: { name: selectedGpc.segment, segmentId: selectedGpc.segmentId },
                family: { name: selectedGpc.family, familyId: selectedGpc.familyId },
                class: { name: selectedGpc.class, classId: selectedGpc.classId },
                brick: { name: selectedGpc.brick, brickId: selectedGpc.brickId }
            }

            $scope.selecionaGpc(formattedSelectedGpc);
            $scope.confirmaSelecaoGpc();
        }

        $scope.limparPesquisa = function () {
            $scope.filter = "";
            $scope.ultimaPesquisaRealizada = "";
            $scope.segmentoFiltrado = "";
            $scope.pesquisaRealizada = false;
        }

        $scope.confirmaSelecaoGpc = function () {
            $scope.itemForm.codesegment = $scope.selectedGpc.segment.segmentId;
            $scope.itemForm.descricaosegmento = $scope.selectedGpc.segment.name;
            $scope.itemForm.codefamily = $scope.selectedGpc.family.familyId;
            $scope.itemForm.descricaofamilia = $scope.selectedGpc.family.name;
            $scope.itemForm.codeclass = $scope.selectedGpc.class.classId;
            $scope.itemForm.descricaoclasse = $scope.selectedGpc.class.name;
            $scope.itemForm.codebrick = $scope.selectedGpc.brick.brickId;
            $scope.itemForm.descricaobrick = $scope.selectedGpc.brick.name;
            $scope.itemForm.segmentoFormatado = $scope.itemForm.codesegment + ' - ' + $scope.itemForm.descricaosegmento;
            $scope.itemForm.familiaFormatada = $scope.itemForm.codefamily + ' - ' + $scope.itemForm.descricaofamilia;
            $scope.itemForm.classeFormatada = $scope.itemForm.codeclass + ' - ' + $scope.itemForm.descricaoclasse;
            $scope.itemForm.brickFormatado = $scope.itemForm.codebrick + ' - ' + $scope.itemForm.descricaobrick;

            $scope.limparPesquisa();

            $scope.definirVisibilidadeDasTelasDeCadastro("formularioProduto");
            $scope.verificarGpcDoTipoNutricional();
        }

        $scope.cancelar = function() {
            $scope.voltarParaTelaDeCadastroAnterior();
        }

        function fetchGpcsMaisUtilizados() {
            genericService.postRequisicao('GpcsUtilizadosBO', 'GetGpcsUtilizados', {}).then(function (response) {
                if (!!response.data.frequentes) {
                    $scope.segmentosFrequentes = response.data.frequentes;
                }

                if (!!response.data.ultimos) {
                    $scope.ultimosGpcs = response.data.ultimos.map(function (ultimoGpc) {
                        return _.find(gpcs, function (gpc) {
                            return gpc.brickId === parseInt(ultimoGpc.codebrick)
                        });
                    });
                }
            });
        }

        fetchGpcsMaisUtilizados();
    }
]);

function mapBrick(brick) {
    var ret = !!brick.item ?
        {
            name: highlightMatchesOnText(brick.item.brick, _.filter(brick.matches, { key: "brick" })) ,
            brickId: brick.item.brickId
        } :
        {
            name: brick.brick,
            brickId: brick.brickId

        };
    return ret;
}

function mapClasse(classEntries) {

    var bricks = classEntries.map(mapBrick);
    var highlightedText = highlightMatchesOnText(classEntries[0].item.class, _.filter(classEntries[0].matches, { key: "class" }));

    return {
        classId: classEntries[0].item.classId,
        name: highlightedText,
        bricks: bricks
    };
}

function mapFamily(familyEntries) {
    var classGroups = _(familyEntries).groupBy("item.classId").map(mapClasse).value();
    var highlightedText = highlightMatchesOnText(familyEntries[0].item.family, _.filter(familyEntries[0].matches, { key: "family" }));
    var containsHighScoreMatches = _.any(familyEntries, function (entry) { return entry.score <= 0.1 });

    return {
        familyId: familyEntries[0].item.familyId,
        name: highlightedText,
        classes: classGroups,
        containsHighScoreMatches: containsHighScoreMatches
    };
}

function mapSegment(segmentEntries) {
    var familyGroups = _(segmentEntries).groupBy("item.familyId").map(mapFamily).value();
    var highlightedText = highlightMatchesOnText(segmentEntries[0].item.segment, _.filter(segmentEntries[0].matches, { key: "segment" }));

    return {
        segmentId: segmentEntries[0].item.segmentId,
        name: highlightedText,
        families: familyGroups
    };
}

function highlightMatchesOnText(text, matches) {
    var charactersAdded = 0;
    var spanHighlightOpen = "<span class='highlight'>";
    var spanHighlightClose = "</span>";
    var spanHighlightCharCount = spanHighlightOpen.length + spanHighlightClose.length;
    _.forEach(matches, function (match) {
        _.forEach(match.indices, function (indice) {
            if (indice[1] - indice[0] < 3) return;
            text =
                text.slice(0, indice[0] + charactersAdded)
                + spanHighlightOpen
                + text.slice(indice[0] + charactersAdded, indice[1] + 1 + charactersAdded)
                + spanHighlightClose
                + text.slice(indice[1] + 1 + charactersAdded, text.length);
            charactersAdded += spanHighlightCharCount;
        });
    });
    return text;
}

function configureSearchEngine(gpcs) {
    var options = {
        shouldSort: true,
        includeMatches: true,
        tokenize: true,
        matchAllTokens: true,
        findAllMatches: true,
        includeScore: true,
        threshold: 0.3,
        location: 0,
        distance: 100,
        maxPatternLength: 32,
        minMatchCharLength: 1,
        keys: [
            "brick",
            "brickId",
            "family",
            "familyId",
            "segment",
            "segmentId",
            "class",
            "classId"
        ]
    };

    return new Fuse(gpcs, options);
}