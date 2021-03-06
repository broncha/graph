<?php

namespace Fhaculty\Graph\Algorithm\TravelingSalesmanProblem;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Algorithm\MinimumSpanningTree\Kruskal as MstKruskal;
use Fhaculty\Graph\Algorithm\Search\BreadthFirst as SearchDepthFirst;

class MinimumSpanningTree extends Base{
    /**
     * 
     * @var Graph
     */
    private $graph;

    public function __construct(Graph $inputGraph){
        $this->graph = $inputGraph;
    }
    
    protected function getVertexStart(){
        return $this->graph->getVertexFirst();
    }
    
    protected function getGraph(){
        return $this->graph;
    }

    /**
     *
     * @return Edge[]
     */
    public function getEdges(){
        $returnEdges = array();

        $minimumSpanningTreeAlgorithm = new MstKruskal($this->graph);          // Create minimum spanning tree
        $minimumSpanningTree = $minimumSpanningTreeAlgorithm->createGraph();

        $alg = new SearchDepthFirst($minimumSpanningTree->getVertexFirst());
        $depthFirstSearch = $alg->getVertices();                                // Depth first search in minmum spanning tree (for the eulerian path)

        $startVertex = NULL;
        $oldVertex = NULL;

        foreach ($depthFirstSearch as $vertex){                                    // connect vertices in order of the depth first search
                
            $vertex = $this->graph->getVertex( $vertex->getId() );                // get vertex from the original graph (not from the depth first search)
                                                                                // need to clone the edge from the original graph, therefore i need the original edge
            if ($startVertex === NULL){
                $startVertex = $vertex;                                            
            }
            else {
                // get edge(s) to clone, multiple edges are possible (returns an array if undirected edge)
                $returnEdges []= Edge::getFirst($oldVertex->getEdgesTo($vertex));
            }
                
            $oldVertex = $vertex;
        }
        
        // connect last vertex with start vertex
        // multiple edges are possible (returns an array if undirected edge)
        $returnEdges []= Edge::getFirst($oldVertex->getEdgesTo($startVertex));
        
        return $returnEdges;
    }
}
