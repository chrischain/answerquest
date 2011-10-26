package model;

import java.io.Serializable;

import java.lang.StringBuilder;

import javax.persistence.Id;
import javax.persistence.NamedQueries;
import javax.persistence.NamedQuery;

import javax.xml.bind.annotation.*;

import javax.persistence.*;

/**
 */

@Entity
@NamedQueries({
		@NamedQuery(name = "findAllResponsess", query = "select myResponses from Responses myResponses"),
		@NamedQuery(name = "findResponsesById", query = "select myResponses from Responses myResponses where myResponses.id = ?1"),
		@NamedQuery(name = "findResponsesByPrimaryKey", query = "select myResponses from Responses myResponses where myResponses.id = ?1"),
		@NamedQuery(name = "findResponsesByQuestionId", query = "select myResponses from Responses myResponses where myResponses.questionId = ?1"),
		@NamedQuery(name = "findResponsesByResponse", query = "select myResponses from Responses myResponses where myResponses.response = ?1"),
		@NamedQuery(name = "findResponsesByResponseContaining", query = "select myResponses from Responses myResponses where myResponses.response like ?1"),
		@NamedQuery(name = "findResponsesByResult", query = "select myResponses from Responses myResponses where myResponses.result = ?1"),
		@NamedQuery(name = "findResponsesBySeriesId", query = "select myResponses from Responses myResponses where myResponses.seriesId = ?1"),
		@NamedQuery(name = "findResponsesByUserId", query = "select myResponses from Responses myResponses where myResponses.userId = ?1") })
@Table(catalog = "HACKATHON", name = "responses")
@XmlAccessorType(XmlAccessType.FIELD)
@XmlType(namespace = "Hackathon/com/model", name = "Responses")
public class Responses implements Serializable {
	private static final long serialVersionUID = 1L;

	/**
	 */

	@Column(name = "id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@Id
	@XmlElement
	Integer id;
	/**
	 */

	@Column(name = "user_id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer userId;
	/**
	 */

	@Column(name = "series_id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer seriesId;
	/**
	 */

	@Column(name = "question_id", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer questionId;
	/**
	 */

	@Column(name = "response", length = 1024, nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	String response;
	/**
	 */

	@Column(name = "result", nullable = false)
	@Basic(fetch = FetchType.EAGER)
	@XmlElement
	Integer result;

	/**
	 */
	public void setId(Integer id) {
		this.id = id;
	}

	/**
	 */
	public Integer getId() {
		return this.id;
	}

	/**
	 */
	public void setUserId(Integer userId) {
		this.userId = userId;
	}

	/**
	 */
	public Integer getUserId() {
		return this.userId;
	}

	/**
	 */
	public void setSeriesId(Integer seriesId) {
		this.seriesId = seriesId;
	}

	/**
	 */
	public Integer getSeriesId() {
		return this.seriesId;
	}

	/**
	 */
	public void setQuestionId(Integer questionId) {
		this.questionId = questionId;
	}

	/**
	 */
	public Integer getQuestionId() {
		return this.questionId;
	}

	/**
	 */
	public void setResponse(String response) {
		this.response = response;
	}

	/**
	 */
	public String getResponse() {
		return this.response;
	}

	/**
	 */
	public void setResult(Integer result) {
		this.result = result;
	}

	/**
	 */
	public Integer getResult() {
		return this.result;
	}

	/**
	 */
	public Responses() {
	}

	/**
	 * Copies the contents of the specified bean into this bean.
	 *
	 */
	public void copy(Responses that) {
		setId(that.getId());
		setUserId(that.getUserId());
		setSeriesId(that.getSeriesId());
		setQuestionId(that.getQuestionId());
		setResponse(that.getResponse());
		setResult(that.getResult());
	}

	/**
	 * Returns a textual representation of a bean.
	 *
	 */
	public String toString() {

		StringBuilder buffer = new StringBuilder();

		buffer.append("id=[").append(id).append("] ");
		buffer.append("userId=[").append(userId).append("] ");
		buffer.append("seriesId=[").append(seriesId).append("] ");
		buffer.append("questionId=[").append(questionId).append("] ");
		buffer.append("response=[").append(response).append("] ");
		buffer.append("result=[").append(result).append("] ");

		return buffer.toString();
	}

	/**
	 */
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = (int) (prime * result + ((id == null) ? 0 : id.hashCode()));
		return result;
	}

	/**
	 */
	public boolean equals(Object obj) {
		if (obj == this)
			return true;
		if (!(obj instanceof Responses))
			return false;
		Responses equalCheck = (Responses) obj;
		if ((id == null && equalCheck.id != null) || (id != null && equalCheck.id == null))
			return false;
		if (id != null && !id.equals(equalCheck.id))
			return false;
		return true;
	}
}
