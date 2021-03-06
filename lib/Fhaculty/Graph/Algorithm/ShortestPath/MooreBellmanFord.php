<?php

namespace Fhaculty\Graph\Algorithm\ShortestPath;

use Fhaculty\Graph\Exception\RuntimeException;

use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Cycle;
use Fhaculty\Graph\Exception\NegativeCycleException;

class MooreBellmanFord extends Base{
    
    /**
     *
     *
     * @param Edge[]   $edges
     * @param int[]    $totalCostOfCheapestPathTo
     * @param Vertex[] $predecessorVertexOfCheapestPathTo
     *
     * @return Vertex|NULL
     */
    private function bigStep(array &$edges,array &$totalCostOfCheapestPathTo,array &$predecessorVertexOfCheapestPathTo){
        $changed = NULL;
        foreach ($edges as $edge){                                                //check for all edges
            foreach($edge->getVerticesTarget() as $toVertex){                        //check for all "ends" of this edge (or for all targetes)
                $fromVertex = $edge->getVertexFromTo($toVertex);
                
                if (isset($totalCostOfCheapestPathTo[$fromVertex->getId()])){            //If the fromVertex already has a path
                    $newCost = $totalCostOfCheapestPathTo[$fromVertex->getId()] + $edge->getWeight(); //New possible costs of this path
                
                    if (! isset($totalCostOfCheapestPathTo[$toVertex->getId()])                //No path has been found yet
                            || $totalCostOfCheapestPathTo[$toVertex->getId()] > $newCost){        //OR this path is cheaper than the old path
                        
                        $changed = $toVertex;
                        $totalCostOfCheapestPathTo[$toVertex->getId()] = $newCost;
                        $predecessorVertexOfCheapestPathTo[$toVertex->getId()] = $fromVertex;
                    }
                }
            }
        }
        return $changed;
    }
    
    /**
     * Calculate the Moore-Bellman-Ford-Algorithm and get all edges on shortest path for this vertex
     * 
     * @return Edge[]
     * @throws NegativeCycleException if there is a negative cycle
     */
    public function getEdges(){
        $totalCostOfCheapestPathTo  = array($this->startVertex->getId() => 0);            //start node distance
        
        $predecessorVertexOfCheapestPathTo  = array($this->startVertex->getId() => $this->startVertex);    //predecessor
        
        $numSteps = $this->startVertex->getGraph()->getNumberOfVertices() - 1; // repeat (n-1) times
        $edges = $this->startVertex->getGraph()->getEdges();
        $changed = true;
        for ($i = 0; $i < $numSteps && $changed; ++$i){                        //repeat n-1 times
            $changed = $this->bigStep($edges,$totalCostOfCheapestPathTo,$predecessorVertexOfCheapestPathTo);
        }
        
        //algorithm is done, build graph
        $returnEdges = $this->getEdgesCheapestPredecesor($predecessorVertexOfCheapestPathTo);
        
        //Check for negative cycles (only if last step didn't already finish anyway)
        if($changed && $changed = $this->bigStep($edges,$totalCostOfCheapestPathTo,$predecessorVertexOfCheapestPathTo)){ // something is still changing...
            $cycle = Cycle::factoryFromPredecessorMap($predecessorVertexOfCheapestPathTo,$changed,Edge::ORDER_WEIGHT);
            throw new NegativeCycleException('Negative cycle found',0,NULL,$cycle);
        }
        
        return $returnEdges;
    }
    
    /**
     * get negative cycle
     * 
     * @return Cycle
     * @throws UnderflowException if there's no negative cycle
     */
    public function getCycleNegative(){
        try{
            $this->getEdges();
        }
        catch(NegativeCycleException $e){
            return $e->getCycle();
        }
        throw new UnderflowException('No cycle found');
    }
}
